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
        //Рекапча
        $reCAPTCHA: $('#google-recaptcha'),
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
        //Кнопки трансформации
        $transformA: $('.transform a'),
        //Фильтры
        $presetFilters: $('#PresetFilters'),
        //Кнопки фильтров
        $presetFiltersA: $('#PresetFilters>a'),
        //Меню редактора
        $cropMenu: $('.crop-menu'),
        //Блоки предпросмотра
        $cropPreview: $('.crop-preview'),
        //Метод вычисляет высоту холдера для картинки
        calcHolderHeight: function (img) {
            var ratio = this.ContainerWidth / img.info.width;
            var height = ratio > 1 ? img.info.height : img.info.height * ratio;
            var width = ratio > 1 ? img.info.width : img.info.width * ratio;
            //TODO - вычислить
            //consoleLog('cw: {}, {}x{}, ratio: {}, ch: {}', this.ContainerWidth, img.info.width, img.info.height, ratio, Math.max(height, width));
            return Math.max(height, width);
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
            //Текст для ввода сообщения
            CropCore.$cropText.hide();
            //Прячем капчу
            CropCore.$reCAPTCHA.hide();
            //Сбрасываем рекапчу
            RecaptureManager.reset();
            //Прячем кнопку публикации
            CropCore.$buttonsBottom.hide();
            //Дизейблим кнопки модификации
            ImageTransform.disable();
            //Сбросим настройки трансформации
            ImageTransform.reset();
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
            CropEditor.startCrop(img, {
                onDone: callback, //Фнукция обратного вызова
                reapplyFilter: true, //Признак повторного применения фильтра
                takeCropBoxData: true //Признак того, что нужно взять прежние настроки crop
            });
        }

        //Сброс crop
        this.cropClear = function (callback) {
            CropEditor.startCrop(img, {
                onDone: callback, //Фнукция обратного вызова
                reapplyFilter: false, //Признак повторного применения фильтра
                takeCropBoxData: false //Признак того, что нужно взять прежние настроки crop
            });
        }

        this.applyTransformDelta = function (transformer) {
            CropEditor.applyTransformDelta(transformer);
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

            if (!RecaptureManager.passed) {
                RecaptureManager.check();
                //CropCore.showError('Не пройдена верификация с помощью капчи.');
                return;//---
            }

            CropLogger.logInfo("Submitting light {}. Emotion: {}. Text: '{}'", img.toString(), emotionCode, text);

            CropCore.progress.start();

            var crop = PsCanvas.cloneAndResize(CropEditor.crop.getCropCanvas(), 240, 240);

            AjaxExecutor.executePost('CropUploadLight', {
                crop: crop.toDataURL(),
                text: text, //Текст
                em: emotionCode, //Код эмоции
                cap: RecaptureManager.response
            },
                    function (ok) {
                        CropCore.hideError();
                        return true;//---
                    },
                    function (err) {
                        CropCore.showError(err);
                        return false;//---
                    },
                    function (isOk) {
                        //Останавливаем прогресс
                        CropCore.progress.stop();
                        //Сбрасываем рекапчу
                        RecaptureManager.reset();
                        if (isOk) {
                            //Очищаем текст сообщения
                            CropCore.$cropTextArea.empty();
                            //Сбросим фильтры
                            ImageFilters.reset();
                            //Закроем редактор
                            CropController.close();
                        }
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
            movable: true,
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
        startCrop: function (img, options) {

            //Запускаем прогресс
            CropCore.progress.start();

            //Опции перестроения
            if (options) {
                options = $.extend({
                    onDone: null, //Фнукция обратного вызова
                    reapplyFilter: true, //Признак повторного применения фильтра
                    takeCropBoxData: true //Признак того, что нужно взять прежние настроки crop
                },
                        options);
            }

            //Обезопасим функцию обратного вызова
            var onDone = PsUtil.safeCall(options ? options.onDone : null);

            //Канвас с изображением, который будет радактироваться
            var canvas = null;

            //Если есть фильтр - применим его
            var filter = null;

            //Инициализационные настройки блока
            var cropBoxData = null;

            //Перестраиваем? Тогда сохраним старый crop, с которого скопируем потом настройки
            if (options) {
                //Берём ли предыдущие настройки?
                cropBoxData = options.takeCropBoxData ? this.crop.$cropper.cropper('getCropBoxData') : null;

                //Применяем фильтры заново?
                if (options.reapplyFilter) {
                    canvas = img.canvasClone();
                    filter = ImageFilters.filter();
                } else {
                    canvas = PsCanvas.clone(this.crop.canvas);
                }
            } else {
                //Уничтожаем текущий crop
                this.stopCrop();

                //Копируем canvas
                canvas = img.canvasClone();
                //Получаем фильтр
                filter = ImageFilters.filter();
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
                applyTransformDelta: function (transformer) {
                    if (!this.$cropper) {
                        return;//---
                    }
                    var disabled = this.$cropper.cropper('getDisabled');
                    if (disabled) {
                        this.setEnabled(true);
                    }
                    transformer(this.$cropper);
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
                    //cropBoxData: cropBoxData,
                    build: function () {
                        //Спрячем предпросмотр
                        CropCore.$cropPreview.setVisibility(false);
                    },
                    built: PsUtil.onceDeferred(function () {
                        CropCore.progress.stop();

                        if (CropController.isCurrent(img)) {
                            this.stopCrop();
                            this.crop = cropNew;
                            ImageTransform.applyAll(cropNew.$cropper);
                            if (cropBoxData) {
                                cropNew.$cropper.cropper('setCropBoxData', cropBoxData);
                            }
                            this.crop.setEnabled(CropEditor.enabled);
                            this.crop.$holder.show();
                            CropCore.$cropPreview.setVisibility(true);
                            onDone();
                            CropController.onCropReady();
                        } else {
                            cropNew.destroy();
                            onDone();
                        }

                    }, CropEditor)
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
        //Метод применяет трансформацию к изображению
        applyTransformDelta: function (transformer) {
            if (this.crop) {
                this.crop.applyTransformDelta(transformer);
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
        //Функция сбрасывает текущий фильтр
        reset: function () {
            CropCore.$presetFiltersA.removeClass('active');
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
        init: function () {
            //Добавить другие кнопки
            CropCore.$transformA.clickClbck(function (href, $a) {
                if (CropCore.progress.isStarted() || $a.is('.disabled')) {
                    return;//---
                }

                if (href == 'close') {
                    PsDialogs.confirm('Закрыть текущее изображение?', CropController.close, CropController);
                    //CropController.close();
                    return;//---
                }

                CropCore.progress.start('transform');

                var onDone = function () {
                    CropCore.progress.stop();
                }

                if (href == 'reset') {
                    this.reset();
                    CropController.cropClear(onDone);
                    return;//---
                }

                var transformer = this.transformer[href];
                if (PsIs.func(transformer)) {
                    CropLogger.logInfo('Применяем трансформацию {}', href);
                    CropController.applyTransformDelta(transformer);
                } else {
                    CropLogger.logWarn('Трансформация {} не найдена', href);
                }

                onDone();

            }, this);
        },
        //Трансформер
        transformer: new function () {
            var stepMove = 10;
            var stepZoom = 0.1;
            var stepRotate = 45;

            //TODO - добавить цепочки действий

            var reflX = false, reflY = false, steps = [];

            //Метод сбрасывает трансформации
            this.reset = function () {
                reflX = false, reflY = false, steps = [];
            };
            //Метод применяет все трансформации сразу
            this.applyAll = function ($cropper) {
                steps.walk(function (arr) {
                    arr = PsArrays.clone(arr);
                    var method = arr.shift()
                    $cropper.cropper(method, arr.length > 0 ? arr[0] : null, arr.length > 1 ? arr[1] : null);
                });
            }
            //Дельты трансформаций
            this.moveL = function ($cropper) {
                $cropper.cropper('move', -stepMove, 0);
                steps.push(['move', -stepMove, 0]);
            }
            this.moveR = function ($cropper) {
                $cropper.cropper('move', stepMove, 0);
                steps.push(['move', stepMove, 0]);
            }
            this.moveU = function ($cropper) {
                $cropper.cropper('move', 0, -stepMove);
                steps.push(['move', 0, -stepMove]);
            }
            this.moveD = function ($cropper) {
                $cropper.cropper('move', 0, stepMove);
                steps.push(['move', 0, stepMove]);
            }
            this.reflectX = function ($cropper) {
                $cropper.cropper('scaleX', reflX ? 1 : -1);
                steps.push(['scaleX', reflX ? 1 : -1]);
                reflX = !reflX;
            }
            this.reflectY = function ($cropper) {
                $cropper.cropper('scaleY', reflY ? 1 : -1);
                steps.push(['scaleY', reflY ? 1 : -1]);
                reflY = !reflY;
            }
            this.zoomIn = function ($cropper) {
                $cropper.cropper('zoom', stepZoom);
                steps.push(['zoom', stepZoom]);
            }
            this.zoomOut = function ($cropper) {
                //zoom = PsMath.sum(zoom, -stepZoom);
                $cropper.cropper('zoom', -stepZoom);
                steps.push(['zoom', -stepZoom]);
            }
            this.rotateL = function ($cropper) {
                $cropper.cropper('rotate', -stepRotate);
                steps.push(['rotate', -stepRotate]);
            }
            this.rotateR = function ($cropper) {
                $cropper.cropper('rotate', stepRotate);
                steps.push(['rotate', stepRotate]);
            }
        },
        reset: function () {
            this.transformer.reset();
        },
        applyAll: function ($cropper) {
            this.transformer.applyAll($cropper);
        },
        disable: function () {
            CropCore.$transformA.addClass('disabled');
        },
        enable: function () {
            CropCore.$transformA.removeClass('disabled');
        }
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
            return CROP.USE_EMOTIONS ? CropCore.$emotionsSpan.filter('.active').data('code') : 0;
        }
    }

    EmotionsManager.init();

    //Управление рекапчей
    var RecaptureManager = {
        //Ответ
        response: null,
        //Признак, пройдена ли капча
        passed: false,
        //Инициализация
        init: function () {
            if (window.gRecaptchaLoaded) {
                this.render();
            } else {
                window.ongRecaptchaLoaded = PsUtil.once(this.render, this);
            }
        },
        //Метод вызывается для показа капчи вместо кнопки отправки сообщения
        check: function () {
            if (this.passed) {
                CropCore.$buttonsBottom.show();
            } else {
                CropCore.$buttonsBottom.fadeOut(500, function () {
                    CropCore.$reCAPTCHA.fadeIn(500);
                });
            }
        },
        //Метод сбрасывает капчу
        reset: function () {
            if (this.passed) {
                this.passed = false;
                grecaptcha.reset();
            }
        },
        //Метод рендерит капчу - вызывается уже после подключения reCAPTURE api
        render: function () {
            //Очищаем placeholder
            CropCore.$reCAPTCHA.empty();
            //Инициализируем рекапчу
            grecaptcha.render('google-recaptcha', {
                sitekey: CROP.CAPTCHA_SITEKEY,
                callback: PsUtil.safeCall(function (response) {
                    this.passed = PsIs.string(response) && response.length > 0;
                    this.response = response;
                    //Удаляем блок, он нам больше не нужен
                    CropCore.$reCAPTCHA.fadeOut(500, function () {
                        //CropCore.$reCAPTCHA.remove();
                        CropCore.$buttonSend.uiButtonDisable();
                        CropCore.$buttonsBottom.fadeIn(500, function () {
                            CropCore.$buttonSend.uiButtonEnable();
                            CropController.submitLight();
                        });
                    });
                }, this)/*,
                 theme: 'dark'*/
            });
        }
    }

    //Показываем меню справа
    CropCore.$cropMenu.setVisibility(true);

    //Закрываем
    CropController.close();

    //Если закрыта возможность добавления изображений - выходим
    if (!CROP.ADD_CELL_ENABLED || defs.isIpBanned) {
        CropCore.showError('Извините, возможность добавления новых ячеек временно закрыта.<br>Ведутся технические работы на сайте.');
        return;//---
    }

    //Инициализируем капчу
    RecaptureManager.init();

    //Стилизуем label
    CropCore.$fileInputLabel.button({
        icons: {
            primary: 'ui-icon-folder-open'
        }
    });

    //Слушатель выбора файла
    CropCore.$fileInput.change(PsUtil.safeCall(FileInput.processSelection, FileInput));

    //Покажем кнопку загрузки файла
    CropCore.$buttonsTop.show();

    //Кнопка отправки сообщения
    CropCore.$buttonSend.button({
        icons: {
            primary: 'ui-icon-mail-closed'
        }
    }).click(CropController.submitLight);

});
