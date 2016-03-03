var CropUtils = {
    //Переводит utc в локальное время
    utc2date: function(utc) {
        if (!PsIs.integer(utc)) return '';
        var timeLeft = '';
        if (false) {
            timeLeft = PsTimeHelper.formatDHMS(new Date().getTime()/1000 - utc);
        }
        return PsTimeHelper.utc2localDateTime(utc) + (timeLeft ? ' ('+ timeLeft + ' назад)' : '');
    },
    
    //Метод подготавливает отображение ячейки
    prepareCellView: function(cellId, $div) {
        var $content = $div.find('.content');
        $content.append($('<div>').text('#'+cellId).addClass('num'));
        var $date = $content.find('.date'); 
        var utc = $date.text();
        if (PsIs.integer(utc)) {
            $date.text(CropUtils.utc2date(utc));
        }
        return $div;
    },
    
    //Хранилище
    STORE: PsLocalStore.inst('crop'),
    //Метод устанавливает признак того, что окно просмотра ячейки может быть закрыто
    setCanClose: function(cellId) {
        this.STORE.set('can-close-'+cellId, 1);
    },
    //Метод возвращает признак - может ли быть закрыто окно просмотра ячейки
    isCanClose: function(cellId) {
        if (this.STORE.has('can-close-'+cellId)) {
            this.STORE.remove('can-close-'+cellId);
            return true;//----
        }
        return false;//---
    }
}


PsUtil.scheduleDeferred(function() {
    var $head = $('h1.head:first');
        
    /*
     * Навигационное меню
     */
    var CropHeaderController = function() {
        
        var $navButtons = $('<div>').addClass('nav-buttons').insertAfter($head);
        function navButtonsAdd(icon, title, href) {
            var $img = $('<img>').attr('src', '/i/png/32x32/'+icon+'.png');
            var $a = $('<a>').attr('title', title).attr('href', PsIs.string(href) ? href : '#').append($img);
            if (PsIs.func(href)) {
                $a.clickClbck(href);
            }
            $navButtons.append($a);
        }
        
        navButtonsAdd('globe', 'Главная', '/');
        navButtonsAdd('add', 'Добавить запись', '/add.php');
        navButtonsAdd('info', 'Информация', '/info.php');
        navButtonsAdd('refresh', 'Обновить страницу', function() {
            location.reload();
        });
        
        /*
        ['puzzle', 'delete'].walk(function(item) {
            navButtonsAdd(item);
        });
         */
        
        var headWidth = $head.width();
        var dimLast = null;
        var scrollLeft = null;
        
        var onResize = function() {
            var docWidth = $(document).width();
            scrollLeft = scrollLeft===null ? $(window).scrollLeft() : scrollLeft;
            if (dimLast && dimLast.dw == docWidth && dimLast.sl==scrollLeft) return;//---
            dimLast = {
                dw: docWidth,
                sl: scrollLeft
            }
            var left = Math.ceil((docWidth - headWidth)/2 + headWidth + (60-32)/2 - scrollLeft);
            $navButtons.css('left', left);
        //consoleLog('docWidth: {}, scrollLeft: {}',docWidth,scrollLeft );
        }
        
        $(window).resize(onResize).scroll(function() {
            var scrollLeftTmp = $(window).scrollLeft();
            if (scrollLeftTmp == scrollLeft) return;//---
            scrollLeft = scrollLeftTmp;
            onResize();
        });
        
        onResize();
        
        $navButtons.show();
    }
    new CropHeaderController();


    /*
     * Блок "поделиться" от яндекса
     * https://tech.yandex.ru/share/
     */
    var YA_SHARE_ID = '#ya-share';
    if (CROP.CROP_YA_SHARE_ENABED) {
        var headHeight = $head.outerHeight();
        $(YA_SHARE_ID).css('top', headHeight + 60 + 2);
        PsUtil.callGlobalObject('Ya', function() {
            var Ya = this;
            Ya.share2(YA_SHARE_ID, {
                theme: {
                    services: PsStrings.trim(CROP.CROP_YA_SHARE_SERVICES).replaceAll(' ', ''),
                    //counter: true,
                    //lang: 'uk',
                    limit: CROP.CROP_YA_SHARE_SERVICES_LIMIT,
                    size: 'm'
                //bare: false
                },
                content: {
                    url: CROP.CROP_YA_SHARE_URL,
                    title: CROP.CROP_YA_SHARE_TITLE,
                    description: CROP.CROP_YA_SHARE_DESCRIPTION,
                    image: CROP.CROP_YA_SHARE_IMAGE
                }
            });
        });
    } else {
        $(YA_SHARE_ID).remove();
    }

});
