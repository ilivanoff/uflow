$(function () {

    var CropLogger = PsLogger.inst('CropUpload').setTrace();

    var CropCore = {
        //Номер выбора
        selectId: 0,
        nextId: function () {
            return ++this.selectId;
        },
        //Контейнер левой части
        $container: $('.container'),
        //Ширина контейнера
        ContainerWidth: $('.container').width(),
        //Верхняя панель кнопок
        $buttonsTop: $('.container .top-buttons'),
        //Поле выбора файла
        $fileInput: $('input#choose-file'),
        //Поле выбора файла
        $fileInputLabel: $('.container .choose-file-label'),
        //Прогресс
        $progress: $('.container .progress'),
        //Блок для показа ошибки
        $error: $('.container .info_box.warn'),
        //Нижняя панель кнопок
        $buttonsBottom: $('.container .bottom-buttons'),
        //Кнопка отправки сообщения
        $buttonSend: $('.container .bottom-buttons button'),
        //Блок с панелью редактирования картинки
        $cropEditor: $('.container .crop-editor'),
        //Выбор эмоции
        $emotions: $('.container .emotions'),
        //Выбор эмоции
        $emotionsSpan: $('.container .emotions>span'),
        //Текст
        $cropText: $('.container .crop-text'),
        $cropTextArea: $('.container .crop-text textarea'),
        //Холдер для блока редактирования картинки
        $croppHolder: $('.crop-holder'),
        //Кнопки переворота
        $rotateBoxA: $('.crop-menu .rotate>a'),
        //Фильтры
        $presetFilters: $('#PresetFilters'),
        //Кнопки фильтров
        $presetFiltersA: $('#PresetFilters>a'),
        //Меню редактора
        $cropMenu: $('.crop-menu'),
        //Метод вычисляет высоту холдера для картинки
        calcHolderHeight: function (img) {
            var ratio = this.ContainerWidth / img.info.width;
            if (ratio > 1)
                return img.info.height;//---
            return img.info.height * ratio;
        },
        //Методы работы с ошибкой
        showError: function (error) {
            CropCore.$error.html($.trim(error)).show();
        },
        hideError: function () {
            CropCore.$error.hide();
        },
        //Инициализация ядра
        init: function () {
            this.progress = new PsUpdateModel(this, function (action) {
                if (!['filter', 'transform'].contains(action)) {
                    this.$progress.show()
                }
                if (action !== 'filter') {
                    ImageFilters.disable();
                }
                this.$fileInputLabel.uiButtonDisable();
                this.$cropTextArea.disable();
                this.$buttonSend.uiButtonDisable();
                CropEditor.setEnabled(false);
                ImageTransform.disable();
            }, function (action) {
                if (!['filter', 'transform'].contains(action)) {
                    this.$progress.hide()
                }
                if (action !== 'filter') {
                    ImageFilters.enable();
                }
                this.$fileInputLabel.uiButtonEnable();
                this.$cropTextArea.enable();
                this.$buttonSend.uiButtonEnable();
                CropEditor.setEnabled(true);
                ImageTransform.enable();
            });
        },
        //Прогресс
        progress: null
    }

    CropCore.init();

    //Если браузер не поддерживает FileApi - показываем ошибку и выходим
    if (!PsCore.hasFileApi) {
        CropCore.showError('К сожалению Ваш браузер устарел и не поддерживает FileApi:(');
        return;//---
    }

    //Контроллер всех элементов
    var CropController = new function () {

        //Текущая картинка
        var img = null;

        //Проверка, является ли картинка текущей
        this.isCurrent = function (id) {
            return PsIs.object(img) && img.id == (PsIs.object(id) && id.hasOwnProperty('id') ? id.id : id);
        }

        //Метод закрывает редактор
        this.close = function () {
            //Стираем информацию о текущем изображении
            img = null;
            //Прекращает прогресс
            CropCore.progress.clear();
            //Прячем ошибку
            CropCore.hideError();
            //Останавливаем редактирование
            CropEditor.stopCrop();
            //Прячем редактор
            CropCore.$cropEditor.hide();
            //Скроем эмоции
            CropCore.$emotions.hide();
            //Тукст для ввода сообщения
            CropCore.$cropText.hide();
            //Прячем кнопку публикации
            CropCore.$buttonsBottom.hide();
            //Дизейблим кнопки модификации
            ImageTransform.disable();
            //Отключаем фильтры
            ImageFilters.disable();
        }

        //Метод вызывается при возниктовении ошибки
        this.onError = function (error) {
            this.close();
            CropCore.showError(error);
        }

        //Метод вызывается, когда была выбрана новая картинка
        this.onImgSelected = function (selected) {
            img = selected;
            CropLogger.logInfo('Пользователь выбрал изображение: {}', selected.toString());
            CropEditor.startCrop(selected);
        }

        this.onCropReady = function () {
            ImageTransform.enable();
            ImageFilters.enable();
            CropCore.$cropText.show();
            CropCore.$emotions.show();
            CropCore.$buttonsBottom.show();
        }

        //Применение фильтров
        this.filterApply = function (callback) {
            CropEditor.startCrop(img, callback, true);
        }

        this.transformApply = function (ob, callback) {
            CropEditor.applyTransform(ob);
            if (callback) {
                callback();
            }
        }

        //Сабмит формы
        this.submitLight = function () {
            var text = CropCore.$cropTextArea.val();
            if (PsIs.empty(text)) {
                CropCore.$cropTextArea.focus();
                return;//---
            }
            if (text.length > CROP.CROP_MSG_MAX_LEN) {
                CropCore.showError('Максимальная длина текста: ' + CROP.CROP_MSG_MAX_LEN + '. Введено: ' + text.length + '.');
                return;//---
            }

            var emotionCode = EmotionsManager.activeCode();
            if (!PsIs.integer(emotionCode)) {
                CropCore.showError('Пожалуйста, выберете эмоцию.');
                return;//---
            }

            CropLogger.logInfo("Submitting light {}. Emotion: {}. Text: '{}'", img.toString(), emotionCode, text);

            CropCore.progress.start();

            var crop = PsCanvas.cloneAndResize(CropEditor.crop.getCropCanvas(), 240, 240);

            AjaxExecutor.executePost('CropUploadLight', {
                crop: crop.toDataURL(),
                text: text, //Текст
                em: emotionCode //Код эмоции
            },
                    CropCore.hideError, CropCore.showError, function () {
                        CropCore.progress.stop();
                    });
        }

        //Сабмит формы
        this.submit = function (text) {
            CropLogger.logInfo("Submitting {} with text: '{}'", img.toString(), text);

            var crop = CropEditor.crop;

            CropCore.progress.start();

            var data = {
                file: {
                    name: img.file.name,
                    type: img.file.type,
                    size: img.file.size,
                    filter: crop.filter
                },
                imgo: img.canvas.toDataURL(), //Оригинальная картинка
                imgf: crop.canvas.toDataURL(), //Изменённая картинка
                imgc: crop.getCropCanvas().toDataURL(), //Обрезанная картинка
                text: text, //Текст
                cropped: crop.getData() //Данные выделения
            }

            //Если оригинальная и изменённая картинка совпадают - не передаём на сервер
            if (data.imgo == data.imgf) {
                delete data['imgf'];
            }

            AjaxExecutor.executePost('CropUpload', data,
                    CropCore.hideError, CropCore.showError, function () {
                        CropCore.progress.stop();
                    });
        }
    }

    //Работа с новым выбранным файлом
    var FileInput = {
        //Обработка выбора
        processSelection: function (evt) {

            var files = FileAPI.getFiles(evt); // Retrieve file list

            //Выбраны ли файлы?
            if (!files.length) {
                CropLogger.logWarn('Файл не выбран');
                return;//---
            }

            CropController.close();

            CropCore.progress.start();

            var id = CropCore.nextId();
            var file = files[0];

            CropLogger.logInfo("$ {}. Выбран файл: '{}' [{}]. Размер: {}.", id, file.name, file.type, file.size);

            var error = this.validateFile(file);
            if (error) {
                CropLogger.logWarn(" ! {}. Файл '{}' не может быть загружен: {}.", id, file.name, error);
                CropController.onError(error);
                return;//---
            }

            FileAPI.getInfo(file, function (err, info) {
                error = err ? err : FileInput.validateFileInfo(info);
                if (error) {
                    CropLogger.logWarn(" ! {}. Файл '{}' не может быть загружен: {}.", id, file.name, error);
                    CropController.onError(error);
                } else {
                    //Подгоним ширину изображения под редактор
                    FileAPI.Image(file).resize(CropCore.ContainerWidth, 600, 'width')
                            .get(function (err, canvas) {
                                if (err) {
                                    CropController.onError('Ошибка обработки изображения: ' + err);
                                } else {
                                    var img = {
                                        id: id,     //Код загрузки
                                        file: file,   //Загруженный файл
                                        info: info,   //Информация об изображении
                                        canvas: canvas, //Объект HTML, по ширине подогнанный для редактора
                                        canvasClone: function () {
                                            return PsCanvas.clone(this.canvas);
                                        },
                                        toString: function () {
                                            return this.id + ".'" + this.file.name + "' [" + this.file.type + "] (" + this.info.width + "x" + this.info.height + ")";
                                        }
                                    };
                                    CropCore.progress.stop();
                                    CropController.onImgSelected(img);
                                }
                            });
                }
            });
        },
        //Метод выполняет превалидацию файла
        validateFile: function (file) {
            if (!file.size) {
                return 'Пустой файл';
            }
            if (!file.type.startsWith('image/')) {
                return 'Данный тип файлов не поддерживается';
            }
            return null;//---
        },
        //Метод проверяет выбранный файл - его тип и размер
        validateFileInfo: function (info) {
            if (!PsIs.object(info) || !PsIs.number(info.width) || !PsIs.number(info.height)) {
                return 'Не удалось получить размер изображения';
            }
            if (info.width <= 0 || info.height <= 0) {
                return 'Некорректный размер изображения: [' + info.width + 'x' + info.height;
            }
            return null;//---
        }
    }

    /*
     * Менеджер редактора видимой области картинки
     */
    var CropEditor = {
        //Объект {$cropper, $holder}
        crop: null,
        //Включено ли редактирование
        enabled: true,
        //Настройки редактора
        cropSettings: {
            aspectRatio: 1,
            preview: '.crop-preview',
            responsive: false,
            background: true,
            autoCropArea: 1,
            movable: false,
            zoomable: true,
            zoomOnWheel: false,
            viewMode: 1
                    /*
                     ,crop: function(e) {
                     $('.crop-preview').empty().each(function() {
                     $(this).append($(e.target).cropper('getCroppedCanvas'));
                     });
                     }*/
        },
        //Метод начинает редактирование картинки в crop
        startCrop: function (img, onDone, rebuild) {

            //Запускаем прогресс
            CropCore.progress.start();

            //Клонируем canvas
            var canvas = img.canvasClone();

            //Если есть фильтр - применим его
            var filter = ImageFilters.filter();

            //Обезопасим функцию обратного вызова
            onDone = PsUtil.safeCall(onDone);

            //У нас может быть старый crop, с которого копируются настройки
            var cropOld = null;

            //Перестраиваем? Тогда сохраним старый crop, с которого скопируем потом настройки
            if (rebuild) {
                cropOld = this.crop;
                //cropOld.setEnabled(false);
            } else {
                //Уничтожаем текущий crop
                this.stopCrop();
            }

            //Инициализируем новый
            var cropNew = {
                filter: filter,
                canvas: canvas,
                $cropper: null,
                $holder: $('<div>').addClass('crop-holder').hide().appendTo(CropCore.$cropEditor).css('height', CropCore.calcHolderHeight(img)).append(canvas),
                getCropCanvas: function () {
                    return this.$cropper ? this.$cropper.cropper('getCroppedCanvas') : null;
                },
                getData: function () {
                    return this.$cropper ? this.$cropper.cropper('getData') : null;
                },
                destroy: function () {
                    if (this.$cropper) {
                        this.$cropper.cropper('destroy');
                    }
                    this.$holder.remove();
                },
                setEnabled: function (enabled) {
                    if (this.$cropper) {
                        this.$cropper.cropper(enabled ? 'enable' : 'disable');
                    }
                },
                applyTransform: function (obj) {
                    if (!this.$cropper) {
                        return;//---
                    }
                    var disabled = this.$cropper.cropper('getDisabled');
                    if (disabled) {
                        this.setEnabled(true);
                    }
                    this.$cropper.cropper(obj.m, obj.d);
                    if (disabled) {
                        this.setEnabled(false);
                    }
                }
            };

            //Покажем редактор
            CropCore.$cropEditor.show();

            var onCanvasReady = function () {
                //Инициализируем панель
                var cropSettings = $.extend({}, CropEditor.cropSettings, {
                    cropBoxData: cropOld ? cropOld.$cropper.cropper('getCropBoxData') : null,
                    built: function () {
                        PsUtil.scheduleDeferred(function () {
                            CropCore.progress.stop();

                            if (CropController.isCurrent(img)) {
                                var oldData = cropOld ? cropOld.getData() : null;
                                this.stopCrop();
                                if (oldData && PsIs.integer(oldData.rotate) && (oldData.rotate != 0)) {
                                    cropNew.$cropper.cropper('rotate', oldData.rotate);
                                }
                                this.crop = cropNew;
                                this.crop.setEnabled(CropEditor.enabled);
                                this.crop.$holder.show();
                                onDone();
                                CropController.onCropReady();
                            } else {
                                cropNew.destroy();
                                onDone();
                            }

                        }, CropEditor);
                    }
                });

                cropNew.$cropper = $(canvas).cropper(cropSettings);
            }

            //Применяем фильтр
            if (filter) {
                Caman(canvas, function () {
                    if (CropController.isCurrent(img)) {
                        this[filter]();
                        this.render(onCanvasReady);
                    }
                });
            } else {
                onCanvasReady();
            }
        },
        //Метод применяет трансформацию в изображению
        applyTransform: function (ob) {
            if (this.crop) {
                this.crop.applyTransform(ob);
            }
        },
        //Метод закрывает редактор
        stopCrop: function () {
            if (this.crop) {
                this.crop.destroy();
                this.crop = null;
            }
        },
        //Включает/отключает перетастивание
        setEnabled: function (enabled) {
            this.enabled = enabled;
            if (this.crop) {
                this.crop.setEnabled(enabled);
            }
        }
    }

    //Фильтры
    var ImageFilters = {
        init: function () {
            CropCore.$presetFiltersA.clickClbck(function () {
                if (CropCore.progress.isStarted() || this.is('.disabled')) {
                    return;//---
                }
                CropCore.$presetFiltersA.not(this.toggleClass('active')).removeClass('active').addClass('disabled');

                CropCore.progress.start('filter');

                //Отключаем фильтры
                CropController.filterApply(function () {
                    ImageFilters.enable();
                    CropCore.progress.stop();
                });

            });
        },
        disable: function () {
            CropCore.$presetFiltersA.addClass('disabled');
        },
        enable: function () {
            CropCore.$presetFiltersA.removeClass('disabled');
        },
        filter: function () {
            return getHrefAnchor(CropCore.$presetFiltersA.filter('.active'));
        },
        hasFilter: function () {
            return !PsIs.empty(this.filter());
        }
    }

    ImageFilters.init();

    //Трансформация картинки
    var ImageTransform = {
        $buttons: null,
        init: function () {
            //Добавить другие кнопки
            this.$buttons = $('.crop-menu .btn-group>a').clickClbck(function (href, $a) {
                if (CropCore.progress.isStarted() || $a.is('.disabled')) {
                    return;//---
                }
                CropCore.progress.start('transform');

                //Отключаем фильтры
                CropController.transformApply(this.registerTransform(href), function () {
                    CropCore.progress.stop();
                });
            }, this);
        },
        /*
         * Задача метода загеристрировать трансформацию в списке всех трансформаций и вернуть объект
         */
        registerTransform: function (href) {
            switch (href) {
                case 'rotateLeft':
                    return {
                        m: 'rotate',
                        d: '-45'
                    }
                case 'rotateRight':
                    return {
                        m: 'rotate',
                        d: '45'
                    }
                case 'zoomPlus':
                    return {
                        m: 'zoom',
                        d: 0.1
                    }
                case 'zoomMinus':
                    return {
                        m: 'zoom',
                        d: -0.1
                    }
            }
        },
        disable: function () {
            this.$buttons.addClass('disabled');
        },
        enable: function () {
            this.$buttons.removeClass('disabled');
        },
    }

    ImageTransform.init();


    //Управление эмоциями
    var EmotionsManager = {
        init: function () {
            CropCore.$emotionsSpan.click(function () {
                CropCore.$emotionsSpan.removeClass('active');
                $(this).addClass('active');
            });
        },
        activeCode: function () {
            return CropCore.$emotionsSpan.filter('.active').data('code');
        }
    }

    EmotionsManager.init();


    //Показываем меню справа
    CropCore.$cropMenu.setVisibility(true);

    //Стилизуем label
    CropCore.$fileInputLabel.button({
        icons: {
            primary: 'ui-icon-folder-open'
        }
    });

    //Слушатель выбора файла
    CropCore.$fileInput.change(PsUtil.safeCall(FileInput.processSelection, FileInput));

    //Закрываем
    CropController.close();

    //Покажем кнопку загрузки файла
    CropCore.$buttonsTop.show();

    //Кнопка отправки сообщения
    CropCore.$buttonSend.button({
        icons: {
            primary: 'ui-icon-mail-closed'
        }
    }).click(CropController.submitLight);

});
