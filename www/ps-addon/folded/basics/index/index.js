//Установим хранилище для ячеек
cells = {};

$(function () {

    var CropLogger = PsLogger.inst('CropUpload').setTrace();

    var CropCore = {
        //Стена
        $wall: $('.top-container .wall'),
        //Области
        $wallAreas: $('.top-container  .wall-areas'),
        //Блок с кнопкой предзагрузки
        $preload: $('.top-container .preload'),
        //Кнопка предзагрузки
        $preloadBtn: $('.top-container .preload button'),
        //Текст кнопки предзагрузки
        preloadBtnTextOriginal: $('.top-container .preload button').text(),
        //Текст на кнопке при загрузке данных
        preloadBtnTextProgress: 'Загружаем...'
    }

    // # 1.
    function MosaicMapController() {
        var $div = null;

        var extractCellId = function ($item) {
            var cellId = $item.data('c');
            if (!PsIs.integer(cellId)) {
                var src = $item.attr('src');
                var srcT = src ? src.split('/', 3) : null;
                cellId = PsIs.array(srcT) && srcT.length >= 3 ? srcT[2] : null;
            }
            return PsIs.integer(cellId) ? 1 * cellId : null;//---
        }

        var onClick = function (e, $item) {
            var cellId = extractCellId($item);
            if (!PsIs.integer(cellId)) {
                return;//---
            }
            //Нет данных или ячейка забанена - пропускаем
            var obj = cells[cellId];
            if (!obj || obj.b) {
                return;//---
            }

            //Положим признак того, что окно может быть закрыто
            CropUtils.setCanClose(cellId);
            //Откроем окно просмотра обсуждения ячейки
            try {
                window.open('/cell.php?id=' + cellId, '_blank');
            } catch (e) {
                CropUtils.isCanClose(cellId);
                throw e;
            }
        }

        var onHide = function () {
            if ($div) {
                $div.remove();
                $div = null;
            }
        }

        var onShow = function (e, $item) {
            onHide();

            var cellId = extractCellId($item);
            if (!PsIs.integer(cellId))
                return;//---

            var obj = cells[cellId];
            //Нет данных или ячейка забанена - пропускаем
            if (!obj) {
                return;//---
            }

            $div = $('<div>').addClass('cell-view popup');

            if (obj.b) {
                $div.append($('<div>').addClass('banned').append(crIMG('/i/gun/barrel.png')).append('&nbsp;Ячейка #' + cellId + ' забанена'));
            } else {
                var src = '/c/' + cellId + '/big.png';
                var $img = $('<img>').addClass('progress').attr('src', CONST.IMG_LOADING_PROGRESS);
                $div.append($img).data('cell', cellId);
                var $content = $('<div>').addClass('content').append($('<div>').addClass('date').text(obj.d)).appendTo($div);
                $content.append($('<div>').html(obj.t.htmlEntities()));

                PsResources.getImgSize(src, function (wh) {
                    $img.attr('src', wh ? src : '/i/blank.png').removeClass('progress');
                });
            }

            $div.append($('<div>').addClass('clearall'));
            $div = CropUtils.prepareCellView(cellId, $div);
            $div.appendTo('body');//.width($div.width());

            onUpdate(e);
        }

        var onUpdate = function (e) {
            if ($div) {
                $div.calculatePosition(e, 3, 3);
            }
        }

        var production = true;
        if (production) {
            PsJquery.on({
                parent: '.wall',
                item: 'map area, img:not(a>img)',
                mouseenter: onShow,
                mousemove: onUpdate,
                mouseleave: onHide,
                click: onClick
            });
        } else {
            onShow({
                pageX: $('.wall')[0].offsetLeft,
                pageY: 150
            },
                    $('.wall img:first'));
        }
    }
    // # 1.

    new MosaicMapController();

    /*
     * Стена
     */
    var Wall = {
        //Счётчик предзагрузок
        preloads: 0,
        //Признак выполнения предзагрузки
        preloading: false,
        //Признак приостановки загрузки при помощи скрола
        stopPreloadOnScroll: false,
        //Время задержки дозагрузки при скроллинге
        stopPreloadDelay: 1000,
        //Метод вызывается для получения последней загруженной группы
        getLastY: function () {
            var y = CropCore.$wall.children('div.gr:last').data('gr');
            return PsIs.integer(y) && y > 0 ? y : null;
        },
        //Можно ли вызывать предзагрузку?
        canPreload: function () {
            var y = this.getLastY();
            return PsIs.integer(y) && y > 1;
        },
        //Метод вызывается для инициализации кнопки дозагрузки данных
        init: function () {

            //Можно и предзагружать?
            if (!this.canPreload()) {
                CropCore.$preload.hide();
                return;//---
            }

            //Показываем кнопку
            CropCore.$preload.show();

            //Инициализируем группу
            CropCore.$preloadBtn.button({
                icons: {
                    primary: 'ui-icon ui-icon-arrowthickstop-1-s'
                }
            }).click(function () {
                Wall.doPreload();
            });

            //Привяжем функцию обновления
            if (true) {
                PsScroll.bindWndScrolledBottom(this.doPreloadScroll, this);
            }
        },
        //Метод вызывается для загрузки порции данных при скроллинге страницы вниз
        doPreloadScroll: function () {
            return this.stopPreloadOnScroll ? false : this.doPreload();
        },
        //Метод вызывается для загрузки порции данных
        doPreload: function () {
            //Если сейчас выполняем предзагрузку - выходим
            if (this.preloading)
                return;//---

            //Можно и предзагружать?
            if (!this.canPreload())
                return;//---

            //Номер загрузки увеличим до дизейбла кнопок
            var preload = ++this.preloads;

            //Устанавливаем признак загрузки
            this.preloading = true;

            //Приостанавливаем загрузку с помощью скролла
            this.stopPreloadOnScroll = true;

            //Дизейблим кнопку и устанавливаем текст
            CropCore.$preloadBtn.uiButtonDisable().uiButtonLabel(CropCore.preloadBtnTextProgress);

            //Функция будет вызвана при окончании загрузки
            var preloadingDone = PsUtil.once(function () {
                //Активируем кнопку предзагрузки (при этом сам блок с кнопкой может быть уже спрятан)
                CropCore.$preloadBtn.uiButtonEnable().uiButtonLabel(CropCore.preloadBtnTextOriginal);
                //Снимаем признак загрузки
                this.preloading = false;
                //В отложенном режиме активируем загрузку скроллом
                PsUtil.scheduleDeferred(function () {
                    //Проверяет, является ли транзакция - текущей
                    if (this.preloads == preload) {
                        this.stopPreloadOnScroll = false;
                    }
                }, this, this.stopPreloadDelay);
            }, this);

            AjaxExecutor.execute('CropWallLoad', {
                ctxt: this,
                y: this.getLastY()
            },
                    function (ok) {
                        var $box = $(ok);
                        var $images = $box.find("img[src^='/']");
                        if (!$images.isEmptySet()) {
                            $box.hide();

                            var allImgsLoaded = PsUtil.once(function () {
                                //Если уже всё загружено - прячем кнопку и отписываемся от скрола
                                if (!this.canPreload()) {
                                    CropCore.$preload.hide();
                                    PsScroll.unbindWndScrolledBottom(this.doPreloadScroll, this);
                                }
                                //Показываем загруженный блок
                                $box.show();
                                //Снимаем состояние предзагрузки
                                preloadingDone();
                            }, this);

                            PsResources.onAllImagesLoaded($images, allImgsLoaded);
                        }

                        CropCore.$wall.append($box);

                        return true;
                    }, 'Загрузка стены', function (ok) {
                if (!ok) {
                    //Снимаем состояние предзагрузки
                    preloadingDone();
                }
            });
        }
    }

    Wall.init();
});
