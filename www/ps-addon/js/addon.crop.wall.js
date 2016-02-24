$(function () {

    var CropLogger = PsLogger.inst('CropUpload').setTrace();

    var CropCore = {
        //Стена
        $wall: $('.top-container .wall'),
        //Блок с кнопкой предзагрузки
        $preload: $('.top-container .preload'),
        //Кнопка предзагрузки
        $preloadBtn: $('.top-container .preload button')
    }

    // # 1.
    function MosaicMapController() {
        var $div = null;

        var onHide = function () {
            if (!$div)
                return;
            $div.remove();
            $div = null;
        }

        var onShow = function (e, $item) {
            onHide();
            var cell = $item.data('c');
            if (!PsIs.integer(cell)) {
                var src = $item.attr('src');
                var srcT = src ? src.split('/', 3) : null;
                cell = PsIs.array(srcT) && srcT.length >= 3 ? srcT[2] : null;
                consoleLog(cell);
            }

            //var ob = defs.cellowners[id];

            /*
             <div class="mosaic-popup">
             <img class="avatar" src="mmedia/avatars/u/u1/22_42x.jpg"/>
             <div class="content">
             <h5>Имя Пользователя</h5>
             <div class="message">Сообщение</div>
             </div>
             <div class="clearall"></div>
             </div>
             */

            $div = $('<div>').addClass('mosaic-popup');
            //$div.append($('<img>').attr('src', $item.attr('src')));
            $div.append($('<img>').attr('src', '/c/' + cell + '/big.png'));
            $div.append($('<div>').addClass('content').text($item.attr('src')));
            /*
             if (ob.msg) {
             $content.append($('<div>').addClass('message').html(ob.msg));
             }
             */
            $div.append($('<div>').addClass('clearall'));
            $div.appendTo('body');//.width($div.width());
            onUpdate(e);
        }
        var onUpdate = function (e) {
            if ($div) {
                $div.calculatePosition(e, 3, 3);
            }
        }

        PsJquery.on({
            parent: '.wall',
            item: 'map area, img:not(a>img)',
            mouseenter: onShow,
            mousemove: onUpdate,
            mouseleave: onHide
        });

        /*
         onShow({
         pageX: $('.wall')[0].offsetLeft,
         pageY: 150
         }, $('.wall img:first'));
         */
    }
    // # 1.


    new MosaicMapController();

    /*
     * Стена
     */
    var Wall = {
        //Кол-во дозагрузок стены
        preloads: 0,
        //Максимальное кол-во автоматических предзагрузок
        PRELOADS_MAX: 3,
        //Метод вызывается для получения последней загруженной группы
        getLastY: function () {
            var y = CropCore.$wall.children('div:last').data('gr');
            return PsIs.integer(y) && y > 0 ? y : null;
        },
        //Метод вызывается для инициализации кнопки дозагрузки данных
        init: function () {

            var y = this.getLastY();

            //Нет групп? Удаляем и выходим.
            if (!y || y <= 1) {
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
            PsScroll.bindWndScrolledBottom(this.doPreload, this);
        },
        //Метод вызывается для загрузки порции данных
        doPreload: function () {
            //Если сделали больше N загрузок - далее пользователь сам должен нажимать кнопки
            if (++this.preloads >= this.PRELOADS_MAX) {
                PsScroll.unbindWndScrolledBottom(this.doPreload);
            }

            CropCore.$preloadBtn.uiButtonDisable();

            AjaxExecutor.execute('CropWallLoad', {
                ctxt: this,
                y: this.getLastY()
            },
                    function (ok) {
                        CropCore.$wall.append(ok);
                    }, 'Загрузка стены',
                    function () {
                        var y = this.getLastY();
                        if (!y || y <= 1) {
                            CropCore.$preload.hide();
                            PsScroll.unbindWndScrolledBottom(this.doPreload, this);
                        } else {
                            CropCore.$preloadBtn.uiButtonEnable();
                        }
                    });
        }
    }

    Wall.init();
});
