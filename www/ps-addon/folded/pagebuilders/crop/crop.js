var CropUtils = {
    //Переводит utc в локальное время
    utc2date: function(utc) {
        return PsIs.integer(utc) ? PsTimeHelper.utc2localDateTime(utc) : '';
    },
    
    //Метод подготавливает отображение ячейки
    prepareCellView: function($div) {
        var $date = $div.find('.content .date'); 
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
    /*
     * Навигационное меню
     */
    var CropHeaderController = function() {
        var $head = $('h1.head:first');
        
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
            consoleLog('docWidth: {}, scrollLeft: {}',docWidth,scrollLeft );
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

});
