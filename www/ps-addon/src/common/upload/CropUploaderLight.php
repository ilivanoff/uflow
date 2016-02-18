<?php

/**
 * Класс отвечает за загрузку изображений в систему
 *
 * @author azaz
 */
class CropUploaderLight {

    /** @var PsLoggerEmpty */
    private $LOGGER;

    /** @var DirManager */
    private $DIR_MANAGER_TMP;

    /**
     * Префикс данных изображения
     */
    const DATA_IMG_PREFIX = 'data:image/png;base64,';

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
     * Загрузка изображений на сервер
     * 
     * @param string $imgo - оригинальное изображение
     * @param string $imgf - изображение с фильтром (может и не быть указано)
     * @param string $imgc - обрезанное изображение
     * @param array $file  - информация о файле
     * @param type $text   - текст сообщения
     */
    public function uploadImpl($dataUrl, $text) {
        if ($this->LOGGER->isEnabled()) {
            $this->LOGGER->info('Dir: ' . $this->DIR_MANAGER_TMP->absDirPath());
            $this->LOGGER->info('Crop len: ' . strlen($dataUrl));
        }

        if (!starts_with($dataUrl, self::DATA_IMG_PREFIX)) {
            die('Invalid image dataUrl given');
        }

        //Выкинем префикс
        $dataUrl = substr($dataUrl, strlen(self::DATA_IMG_PREFIX));

        //Дополнительная обработка на чистоту ссылки
        $dataUrl = base64_decode($dataUrl);

        //Создаём изображение из строки
        $imBig = imagecreatefromstring($dataUrl);
        $imSmall = null;
        try {

            //Получим параметры изображения
            $w = imagesx($imBig);
            $h = imagesy($imBig);

            $this->LOGGER->info('Image dimensions: {}x{}', $w, $h);

            //Проверим размеры изображения
            if ($w != self::CROP_SIZE_BIG || $w != $h) {
                return PsUtil::raise('Invalid crop upload size: {}x{}', $w, $h);
            }

            //Сохраним уменьшенное изображение
            $imSmall = imagecreatetruecolor(self::CROP_SIZE_SMALL, self::CROP_SIZE_SMALL);
            check_condition($imSmall, 'Cannot image create true color');
            $success = imagecopyresampled($imSmall, $imBig, 0, 0, 0, 0, self::CROP_SIZE_SMALL, self::CROP_SIZE_SMALL, $w, $h);
            check_condition($success, 'Cannot create thumbnail');
            $success = imagepng($imSmall, $absPathSmall = $this->DIR_MANAGER_TMP->absFilePath(null, self::TMP_FILE_SMALL, PsConst::EXT_PNG));
            check_condition($success, 'Cannot save thumbnail');
            @imagedestroy($imSmall);
            $imSmall = null;

            //Сохраним полученное с клиента изображение
            $success = imagepng($imBig, $absPathBig = $this->DIR_MANAGER_TMP->absFilePath(null, self::TMP_FILE_BIG, PsConst::EXT_PNG));
            check_condition($success, 'Cannot save cropped image');
            @imagedestroy($imBig);
            $imBig = null;

            //Бронируем ячейку
            $cellId = CropCellsManager::inst()->bindCell($this->DIR_MANAGER_TMP->getDirName(), $text);

            //Копируем файлы в директорию
            $DEST_DM = DirManagerCrop::cropAuto($cellId);

            //Копируем файлы в конечную директорию
            /*
             * TODO - проверить, что удалось скопировать
             * TODO - обновить признак удачной привязки
             */
            copy($absPathBig, $DEST_DM->absFilePath(null, self::TMP_FILE_BIG, PsConst::EXT_PNG));
            copy($absPathSmall, $DEST_DM->absFilePath(null, self::TMP_FILE_SMALL, PsConst::EXT_PNG));

            //Подтверждаем ячейку
            CropCellsManager::inst()->subitCell($cellId);

            //Чистим временную директорию
            $this->DIR_MANAGER_TMP->clearDir(null, true);
        } catch (Exception $ex) {
            /*
             * Обязательно уничтожаем изображение!
             */
            if ($imSmall) {
                @imagedestroy($imSmall);
                $imSmall = null;
            }
            if ($imBig) {
                @imagedestroy($imBig);
                $imBig = null;
            }
            /*
             * Удаляем временную директорию
             */
            $this->DIR_MANAGER_TMP->clearDir(null, true);
            /*
             * Логируем ошибку
             */
            $this->LOGGER->info('Crop processing error: {}', $ex->getMessage());
            ExceptionHandler::dumpError($ex);
            die('Crop processing error: ' . $ex->getMessage());
        }

        //В данном месте мы должны уже освободить ресурсы
        PsCheck::_null($imBig);
    }

    /**
     * Метод вызывается для загрузки изображения
     */
    public static function upload($crop, $text) {
        return (new CropUploaderLight())->uploadImpl(PsCheck::notEmptyString($crop), PsCheck::notEmptyString($text));
    }

    /**
     * Конструктор
     */
    private function __construct() {
        $this->LOGGER = PsLogger::inst(__CLASS__);
        $this->DIR_MANAGER_TMP = DirManagerCrop::cropTempDir();
    }

}
