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
        var $content = $div.find('.cell-content');
        $content.append($('<div>').text('#'+cellId).addClass('num').attr('title', 'Номер ячейки'));
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
    var $header = $('header');
    
    //Подсветим текущую страницу
    var script = PsUrl.getPhpScriptName();
    $header.find('nav a[href="/'+(script=='index.php' ? '' : script)+'"]').addClass('active');
    
    //Управление кнопками навинации
    var NavController = function() {
        var $nav = $('nav');
        
        var onScroll = function() {
            $nav.css('top', $(window).scrollTop());
        }
        
        $(window).resize(onScroll).scroll(onScroll);
        
        onScroll();
    };
    //new NavController();

    /*
     * Блок "поделиться" от яндекса
     * https://tech.yandex.ru/share/
     */
    var YA_SHARE_ID = '#ya-share';
    if (CROP.CROP_YA_SHARE_ENABED) {
        var headHeight = $header.outerHeight();
        $(YA_SHARE_ID).css('top', headHeight+2);
        PsUtil.callGlobalObject('Ya', function() {
            var Ya = this;
            Ya.share2(YA_SHARE_ID.removeFirstChar(), {
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
