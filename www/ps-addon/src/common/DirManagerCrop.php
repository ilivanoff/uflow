<?php

/**
 * Description of DirManagerCrop
 *
 * @author azaz
 */
class DirManagerCrop {

    const DIR_CROP = 'c';
    const DIR_TEMP = 'd';
    const DIR_CROP_TEST = 'testcrops';

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
     * Метод возвращает временную директорию для работы и сохранения изображений
     * 
     * @return DirManager
     */
    public static function cropTemp() {
        return DirManager::inst(null, self::DIR_TEMP . DIR_SEPARATOR . PsUtil::fileUniqueTime());
    }

}
