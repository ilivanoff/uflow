$(function () {
    //Подготовим отображение
    CropUtils.prepareCellView($('.cell-view'));

    
    VK.init({
        apiId: CROP.CROP_VK_API_ID, 
        onlyWidgets: true
    });
    
    //Кнопка лайк
    VK.Widgets.Like("vk_like", {
        type: "button"
    });
    
    //Подготовим панель с комментариями
    VK.Widgets.Comments('vk_comments', {
        limit: 10, 
        attach: '*',
        pageUrl: '/?id='+CELL_ID
    });
    
/*
    VK.Widgets.Share('vk_share', {
        type: "round", 
        text: "Сохранить"
    });
    */
});