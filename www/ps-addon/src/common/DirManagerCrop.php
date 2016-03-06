<?php

/**
 * Description of DirManagerCrop
 *
 * @author azaz
 */
class DirManagerCrop {

    const DIR_CROP = 'c';
    const DIR_TEMP = 'd';
    const DIR_GROUP = 'g';
    const DIR_IMGS = 'i';
    const DIR_CROP_TEST = 'testcrops';

    /**
     * Директория, в которой хранятся изображения
     */
    public static function cropsDir() {
        return DirManager::inst(self::DIR_CROP);
    }

    /**
     * Директория, в которой хранятся временные файлы загружаемых изображений
     */
    public static function tempsDir() {
        return DirManager::inst(self::DIR_TEMP);
    }

    /**
     * Директория, в которой хранятся сгруппированные изображения
     */
    public static function groupsDir() {
        return DirManager::inst(self::DIR_GROUP);
    }

    /**
     * Элемент группы - изображение, хранящее сгруппированные изображения группы
     */
    public static function groupFile($y) {
        return self::groupsDir()->getDirItem(null, PsCheck::int($y), CropConst::CROP_EXT);
    }

    /**
     * Директория хранения картинок
     * 
     * @param int $cellId - код картинки
     * @return DirManager
     */
    public static function cropAuto($cellId) {
        return DirManager::inst(null, self::DIR_CROP . DIR_SEPARATOR . PsCheck::positiveInt($cellId));
    }

    /**
     * Метод проверяет существование картинки
     * 
     * @param int $cellId - код картинки
     * @return bool
     */
    public static function cropExists($cellId) {
        return is_dir(PATH_BASE_DIR . self::DIR_CROP . DIR_SEPARATOR . PsCheck::positiveInt($cellId));
    }

    /**
     * Метод проверяет существование группы
     * 
     * @param int $y - код группы
     * @return bool
     */
    public static function groupExists($y) {
        return self::groupFile($y)->isImg();
    }

    /**
     * Метод проверяет существование изображения ячейки
     * 
     * @param int $cellId - код ячейки
     * @return bool
     */
    public static function imgExists($cellId, $img = CropConst::TMP_FILE_BIG) {
        return PsImg::isImg(PATH_BASE_DIR . self::DIR_CROP . DIR_SEPARATOR . $cellId . DIR_SEPARATOR . $img . '.' . CropConst::CROP_EXT);
    }

    /**
     * Метод возвращает временную директорию для работы и сохранения изображений
     * 
     * @return DirManager
     */
    public static function cropTempAuto() {
        return DirManager::inst(null, self::DIR_TEMP . DIR_SEPARATOR . PsUtil::fileUniqueTime());
    }

    /**
     * Относительный путь к ячейке
     */
    private static function cropRel($cellId, $img = CropConst::TMP_FILE_BIG) {
        return '/' . self::DIR_CROP . '/' . $cellId . '/' . $img . '.' . CropConst::CROP_EXT;
    }

    /**
     * Относительный путь к big ячейке
     */
    public static function cropRelBig($cellId) {
        return self::cropRel($cellId, CropConst::TMP_FILE_BIG);
    }

    /**
     * Относительный путь к small ячейке
     */
    public static function cropRelSmall($cellId) {
        return self::cropRel($cellId, CropConst::TMP_FILE_SMALL);
    }

    /**
     * Big ячейка бана
     */
    public static function banDiBig() {
        return DirItem::inst(self::DIR_IMGS, 'bb', CropConst::CROP_EXT);
    }

    /**
     * Small ячейка бана
     */
    public static function banDiSmall() {
        return DirItem::inst(self::DIR_IMGS, 'bs', CropConst::CROP_EXT);
    }

}
