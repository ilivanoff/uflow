$(function () {
    //Подготовим отображение
    CropUtils.prepareCellView(defs.cell_id, $('.cell-view'));

    $('.cell-view img').click(function () {
        window.close()
    });


    VK.init({
        apiId: CROP.CROP_VK_API_ID,
        onlyWidgets: true
    });

    //Кнопка лайк
    CropUtils.initVkLike(defs.cell_id);

    //Подготовим панель с комментариями
    VK.Widgets.Comments('vk_comments', {
        limit: 10,
        attach: '*',
        pageUrl: '/cell.php?id=' + defs.cell_id
    });

    /*
     VK.Widgets.Share('vk_share', {
     type: "round", 
     text: "Сохранить"
     });
     */
});