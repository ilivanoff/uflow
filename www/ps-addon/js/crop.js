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