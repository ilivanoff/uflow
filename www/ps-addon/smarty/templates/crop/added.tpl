<div class="cell-added">
    <h3>Ячейка #{$cell->getCellId()} успешно добавлена на стену</h3>
    <p>
        Интерфейс управления ячейками пока не доступен. Если возникла необходимость отредактировать текст
        или внести другие изменения, можно связаться с нами по адресу <a href="mailto:{$smarty.const.CROP_SUPPORT_MAIL}">{$smarty.const.CROP_SUPPORT_MAIL}</a>.
    </p>
    <p>
        В письме просьба указать оставленный Вами email <b>{$cell->getMail()}</b> и номер ячейки <b>#{$cell->getCellId()}</b>.
        Также просьба указывать тему сообщения: <b>Модификация ячейки</b>.
    </p>
    <a href="/" class="goto-wall">
        <span class="fa fa-hand-o-left"></span> перейти к стене
    </a>
</div>