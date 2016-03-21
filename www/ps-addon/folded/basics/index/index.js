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
        var $div = null, onHideTimer = null;

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

        var onBubbleHover = function () {
            onHideTimer.stop();
        }

        var onHide = function () {
            if ($div) {
                $div.remove();
                $div = null;
            }
            if (onHideTimer) {
                onHideTimer.stop();
            }
        }

        onHideTimer = new PsTimerAdapter(onHide, 10);

        var onHideDeferred = function () {
            onHideTimer.start();
        }

        var onShow = function (e, $target) {
            onHide();

            var cellId = extractCellId($target);
            if (!PsIs.integer(cellId))
                return;//---

            var obj = cells[cellId];
            //Нет данных или ячейка забанена - пропускаем
            if (!obj) {
                return;//---
            }

            $div = $('<div>').addClass('cell-view popup');

            //Ячейка забанена? Показываем окошко с информацией и выходим
            if (obj.b) {
                $div.addClass('banned').append(crIMG('/i/gun/barrel.png')).append('&nbsp;Ячейка #' + cellId + ' забанена').appendTo('body').calculatePosition(e, 3, 3);
                return;//---
            }

            var src = '/c/' + cellId + '/big.png';
            var $img = $('<img>').addClass('progress').attr('src', CONST.IMG_LOADING_PROGRESS).click(onHide);
            $div.append($img).data('cell', cellId);
            var $content = $('<div>').addClass('cell-content').appendTo($div);
            //Дата
            $content.append($('<div>').addClass('date').text(obj.d))
            //Текст
            if (obj.t) {
                $content.append($('<div>').addClass('text').html(obj.h ? obj.t : obj.t.htmlEntities()));
            }
            //Автор
            if (obj.a) {
                $content.append($('<div>').addClass('auth').text(obj.a));
            }

            PsResources.getImgSize(src, function (wh) {
                $img.attr('src', wh ? src : '/i/blank.png').removeClass('progress');
            });

            $div.append($('<div>').addClass('clearall'));
            $div = CropUtils.prepareCellView(cellId, $div);
            $div.appendTo('body');//.width($div.width());

            //onUpdate(e);

            function attach() {
                var $targetOffset = $target.offset();

                var cellTop = $targetOffset.top;
                var cellLeft = $targetOffset.left;

                if ($target.is('area')) {
                    var wallOffs = CropCore.$wall.offset();
                    cellLeft = wallOffs.left + 1 * $target.attr('coords').split(',')[0];
                }

                cellTop = Math.round(cellTop);
                cellLeft = Math.round(cellLeft);

                var offsetX = -10, offsetY = -10;
                var trgtWidth = 60, trgtHeight = 60; //Ширина и высота элемента, на который наведён курсор (для !byEvent)

                var pageX = cellLeft + trgtWidth;
                var pageY = cellTop + trgtHeight; //Точка, относительно которой необходим показ

                var divWidth = $div.psOuterWidth(true);
                var divHeight = $div.outerHeight(true);
                var winWidth = $(window).width();
                var winHeight = $(window).height();
                var winLeft = $(window).scrollLeft();
                var winTop = $(window).scrollTop();

                var left = pageX + offsetX; //Факчитеское расстояние до точки показа по горизонтали (с учётом сдвига)
                if (left + divWidth > winLeft + winWidth) {
                    var newLeft = left - 2 * offsetX - divWidth - trgtWidth;
                    if (newLeft < winLeft) {
                        //Выяснилось, что новая позиция левого края также не помещается в экран
                        //Вычислим ширину видимой области для обоих положений и сравним их.
                        //Если в старом варианте (left) видно больше, чем в новом, то оставим старый вариант.
                        var xbounds1 = PsMath.bounds(left, left + divWidth, winLeft, winLeft + winWidth);
                        var xvis1 = xbounds1[1] - xbounds1[0];
                        var xbounds2 = PsMath.bounds(newLeft, newLeft + divWidth, winLeft, winLeft + winWidth);
                        var xvis2 = xbounds2[1] - xbounds2[0];
                        if (xvis1 < xvis2) {
                            left = newLeft;
                        }
                    } else {
                        left = newLeft;
                    }
                }

                var top = pageY + offsetY; //Факчитеское расстояние от верха страницы до курсора по вертигаки (с учётом сдвига)
                if ((top + divHeight > winTop + winHeight) && (top > divHeight)) { //Если при показе элемента вверх он также не влезает, то покажем его вниз
                    top = top - 2 * offsetY - divHeight - trgtHeight;
                }

                //Вычислим угол, которым bubble прикреплён к ячейку
                var xPos = left > cellLeft ? 'l' : 'r';
                var yPos = top > cellTop ? 'u' : 'b';

                $div.css('left', left).css('top', top).addClass(xPos + yPos);
            }

            attach();
            onUpdate(e);
        }

        var onUpdate = function (e) {
            if ($div && $div.is('.banned')) {
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
                mouseleave: onHideDeferred,
                click: onShow
            });

            PsJquery.on({
                parent: 'body',
                item: '.cell-view',
                mouseenter: onBubbleHover,
                mouseleave: onHide
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
