<?php

/**
 * Регистратор страниц
 *
 * @author azazello
 */
class WebPagesStorageCrop extends WebPagesStorage {

    protected function registerProjectPages() {
        $this->register('/index.php', 'Главная страница', BASE_PAGE_INDEX, PB_crop::getIdent());
        $this->register('/add.php', 'Добавление ячейки', CROP_PAGE_ADD, PB_crop::getIdent());
    }

}

?>