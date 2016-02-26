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
        $preloadBtn: $('.top-container .preload button')
    }

    // # 1.
    function MosaicMapController() {
        var $div = null;
        
        var extractCellId = function($item) {
            var cellId = $item.data('c');
            if (!PsIs.integer(cellId)) {
                var src = $item.attr('src');
                var srcT = src ? src.split('/', 3) : null;
                cellId = PsIs.array(srcT) && srcT.length >= 3 ? srcT[2] : null;
            }
            return PsIs.integer(cellId) ? 1*cellId : null;//---
        }
        
        var onClick = function(e, $item) {
            var cellId = extractCellId($item);
            if(!PsIs.integer(cellId)) {
                return;//---
            }
            //Положим признак того, что окно может быть закрыто
            CropUtils.setCanClose(cellId);
            //Откроем окно просмотра обсуждения ячейки
            try{
                window.open('/?id='+cellId, '_blank');
            } catch(e) {
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
            if (!PsIs.integer(cellId)) return;//---
            
            var obj = cells[cellId];
            if(!obj) return;//---
            
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
            var src = '/c/' + cellId + '/big.png';
            var $img = $('<img>').addClass('progress').attr('src', CONST.IMG_LOADING_PROGRESS);

            $div = $('<div>').addClass('cell-view popup');
            //$div.append($('<img>').attr('src', $item.attr('src')));
            $div.append($img).data('cell', cellId);
            $div.append($('<div>').addClass('content').append($('<div>').addClass('date').text(obj.d)).append($('<div>').html(obj.t.htmlEntities())));
            /*
             if (ob.msg) {
             $content.append($('<div>').addClass('message').html(ob.msg));
             }
             */
            $div.append($('<div>').addClass('clearall'));
            $div = CropUtils.prepareCellView($div);
            $div.appendTo('body');//.width($div.width());

            onUpdate(e);

            PsResources.getImgSize(src, function () {
                $img.attr('src', src).removeClass('progress');
            });
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
            }, $('.wall img:first'));
        }
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
            var y = CropCore.$wall.children('div.gr:last').data('gr');
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
            if (this.PRELOADS_MAX>0) {
                PsScroll.bindWndScrolledBottom(this.doPreload, this);
            }
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
