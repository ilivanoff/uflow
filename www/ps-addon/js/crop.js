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
    }
}


PsUtil.scheduleDeferred(function() {
    /*
     * Навигационное меню
     */
    var CropHeaderController = function() {
        var $head = $('h1.head:first');
        
        var $navButtons = $('<div>').addClass('nav-buttons').insertAfter($head);
        function navButtonsAdd(icon, href) {
            var $img = $('<img>').attr('src', '/i/png/32x32/'+icon+'.png');
            var $a = $('<a>').attr('href', href ? href : '#').append($img);
            $navButtons.append($a);
        }
        
        navButtonsAdd('globe', '/');
        navButtonsAdd('add', '/index.php?page=img');
        
        ['puzzle', 'info', 'delete'].walk(function(item) {
            navButtonsAdd(item);
        });
        
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
