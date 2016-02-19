<?php

/**
 * Description of DirManagerCrop
 *
 * @author azaz
 */
class DirManagerCrop {

    const DIR_CROPS = 'crops';

    /**
     * Директория хранения картинок
     * 
     * @param int $cellId - код картинки
     * @return DirManager
     */
    public static function cropAuto($cellId) {
        return DirManager::inst(self::DIR_CROPS, PsCheck::positiveInt($cellId));
    }

    /**
     * Метод проверяет существование картинки
     * 
     * @param int $cellId - код картинки
     * @return bool
     */
    public static function cropExists($cellId) {
        return is_dir(PATH_BASE_DIR . self::DIR_CROPS . DIR_SEPARATOR . PsCheck::positiveInt($cellId));
    }

    /**
     * Метод возвращает временную директорию для работы и сохранения изображений
     * 
     * @return DirManager
     */
    public static function cropTempDir() {
        return DirManager::inst(self::DIR_CROPS . '/temp', PsUtil::fileUniqueTime());
    }

}
