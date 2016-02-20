<?php

/**
 * Константы подсистемы
 *
 * @author azazello
 */
class CropConst {

    /**
     * Максимальное кол-во ячеек в группе (960/60)
     */
    const CROPS_GROUP_CELLS = 16;

    /**
     * Ширина группы ячеек
     */
    const CROPS_GROUP_WIDTH = 960;

    /**
     * Размер загруженного изображения
     */
    const CROP_SIZE_BIG = 240;
    const CROP_SIZE_SMALL = 60;

    /**
     * Названия временных файлов
     */
    const TMP_FILE_BIG = 'big';
    const TMP_FILE_SMALL = 'small';

    /**
     * Расширение файлов
     */
    const CROP_EXT = PsConst::EXT_PNG;

    /**
     * Порция загружаемых групп
     */
    const GROUPS_LOAD_PORTION = 16;

}

?>
