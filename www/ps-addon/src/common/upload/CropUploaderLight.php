<?php

/**
 * Класс отвечает за загрузку изображений в систему
 *
 * @author azaz
 */
class CropUploaderLight {

    /** @var PsLoggerEmpty */
    private $LOGGER;

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
            $this->LOGGER->info('Crop len: ' . strlen($dataUrl));
        }

        if (!starts_with($dataUrl, self::DATA_IMG_PREFIX)) {
            return PsUtil::raise('Invalid image dataUrl given');
        }

        //Выкинем префикс
        $dataUrl = substr($dataUrl, strlen(self::DATA_IMG_PREFIX));

        //Дополнительная обработка на чистоту ссылки
        $dataUrl = base64_decode($dataUrl);

        //Создаём изображение из строки
        $imBig = imagecreatefromstring($dataUrl);
        $imSmall = null;

        //Создаём временную директорию
        $DM_TEMP = DirManagerCrop::cropTemp();
        //Признак удаления временной директории на ошибку
        $DM_TEMP_CLEAR = true;
        $this->LOGGER->info('Temp dir: ' . $DM_TEMP->relDirPath());
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
            $success = imagepng($imSmall, $absPathSmall = $DM_TEMP->absFilePath(null, self::TMP_FILE_SMALL, PsConst::EXT_PNG));
            check_condition($success, 'Cannot save thumbnail');
            @imagedestroy($imSmall);
            $imSmall = null;

            //Сохраним полученное с клиента изображение
            $success = imagepng($imBig, $absPathBig = $DM_TEMP->absFilePath(null, self::TMP_FILE_BIG, PsConst::EXT_PNG));
            check_condition($success, 'Cannot save cropped image');
            @imagedestroy($imBig);
            $imBig = null;

            //Бронируем ячейку
            $cellId = CropCellsManager::inst()->bindCell($DM_TEMP->getDirName(), $text);
            //Временную директорию до подтверждения ячейки удалять
            $DM_TEMP_CLEAR = false;

            //Копируем файлы в директорию
            $DM_DEST = DirManagerCrop::cropAuto($cellId);
            $this->LOGGER->info('Dest dir: ' . $DM_DEST->relDirPath());

            //Копируем файлы в конечную директорию
            $success = copy($absPathBig, $DM_DEST->absFilePath(null, self::TMP_FILE_BIG, PsConst::EXT_PNG));
            if (!$success) {
                $DM_DEST->removeDir();
                return PsUtil::raise('Cannot copy crop image');
            }
            $success = copy($absPathSmall, $DM_DEST->absFilePath(null, self::TMP_FILE_SMALL, PsConst::EXT_PNG));
            if (!$success) {
                $DM_DEST->removeDir();
                return PsUtil::raise('Cannot copy thumbnail');
            }

            //Подтверждаем ячейку
            CropCellsManager::inst()->submitCell($cellId);

            //Временная директория теперь не нужна
            $DM_TEMP_CLEAR = true;

            //Чистим временную директорию
            $DM_TEMP->removeDir();

            //В данном месте мы должны уже освободить ресурсы
            PsCheck::_null($imSmall);
            PsCheck::_null($imBig);

            //Возвращаем код ячейки
            return $cellId; //---
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
             * Чистим временную директорию, если необходимо
             */
            if ($DM_TEMP_CLEAR) {
                $DM_TEMP_CLEAR = false;
                $DM_TEMP->removeDir();
            }
            /*
             * Логируем ошибку
             */
            $errMsg = 'Crop processing error: ' . $ex->getMessage();
            $this->LOGGER->info($errMsg);
            /*
             * Снимаем дамп
             */
            ExceptionHandler::dumpError($ex);
            /*
             * Пробрасываем
             */
            return PsUtil::raise($errMsg);
        }
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
    }

}
