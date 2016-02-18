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
     * @param int $code - код картинки
     * @return DirManager
     */
    public static function cropAuto($code) {
        return DirManager::inst(self::DIR_CROPS, PsCheck::int($code));
    }

    /**
     * Метод проверяет существование картинки
     * 
     * @param int $code - код картинки
     * @return bool
     */
    public static function cropExists($code) {
        return is_dir(PATH_BASE_DIR . self::DIR_CROPS . DIR_SEPARATOR . PsCheck::int($code));
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
