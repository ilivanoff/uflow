<?php

/**
 * Класс отвечает за загрузку изображений в систему
 *
 * @author azaz
 */
class CropUploaderLight {
    /**
     * Префикс данных изображения
     */

    const DATA_IMG_PREFIX = 'data:image/png;base64,';

    /**
     * Загрузка изображений на сервер
     * 
     * @param string $imgo - оригинальное изображение
     * @param string $imgf - изображение с фильтром (может и не быть указано)
     * @param string $imgc - обрезанное изображение
     * @param array $file  - информация о файле
     * @param type $text   - текст сообщения
     * @return CropCell Ячейка
     */
    public static function upload($dataUrl, $text, $em) {
        $LOGGER = PsLogger::inst(__CLASS__);

        $emName = CropConst::getEmotionName($em);

        $LOGGER->info();
        $LOGGER->info('Uploading image. Emotion: {} (code: {}). Text: \'{}\' (len: {}).', $emName, $em, $text, ps_strlen($text));

        if ($LOGGER->isEnabled()) {
            $LOGGER->info('Crop len: ' . strlen($dataUrl));
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

        //Признак удаления временной директории на ошибку
        $DM_TEMP = null;
        $DM_TEMP_CLEAR = true;
        try {

            //Получим параметры изображения
            $w = imagesx($imBig);
            $h = imagesy($imBig);

            $LOGGER->info('Image dimensions: {}x{}', $w, $h);

            //Проверим размеры изображения
            if ($w != CropConst::CROP_SIZE_BIG || $w != $h) {
                return PsUtil::raise('Invalid crop upload size: {}x{}', $w, $h);
            }

            //Сохраним уменьшенное изображение
            $imSmall = imagecreatetruecolor(CropConst::CROP_SIZE_SMALL, CropConst::CROP_SIZE_SMALL);
            check_condition($imSmall, 'Cannot image create true color');
            imagefill($imSmall, 0, 0, imagecolorallocate($imSmall, 255, 255, 255));
            $success = imagecopyresampled($imSmall, $imBig, 0, 0, 0, 0, CropConst::CROP_SIZE_SMALL, CropConst::CROP_SIZE_SMALL, $w, $h);
            check_condition($success, 'Cannot create thumbnail');

            // TODO - подумать насчёт unlimited mode
            //Создаём временную директорию. В случае ошибки она будет удалена
            $DM_TEMP = DirManagerCrop::cropTempAuto();
            $LOGGER->info('Temp dir: {}', $DM_TEMP->relDirPath());

            //Начинаем создание ячеек

            $success = imagepng($imSmall, $absPathSmall = $DM_TEMP->absFilePath(null, CropConst::TMP_FILE_SMALL, CropConst::CROP_EXT));
            check_condition($success, 'Cannot save thumbnail');
            @imagedestroy($imSmall);
            $imSmall = null;

            //Сохраним прозрачность
            $success = imagesavealpha($imBig, true);
            check_condition($success, 'Cannot image save alpha');
            //Сохраним полученное с клиента изображение
            $success = imagepng($imBig, $absPathBig = $DM_TEMP->absFilePath(null, CropConst::TMP_FILE_BIG, CropConst::CROP_EXT));
            check_condition($success, 'Cannot save cropped image');
            @imagedestroy($imBig);
            $imBig = null;

            //Временную директорию до подтверждения ячейки не удалять
            $DM_TEMP_CLEAR = false;

            //Бронируем ячейку
            $cell = CropCellsManager::inst()->bindCell($DM_TEMP->getDirName(), $text);

            $LOGGER->info('{}', $cell);

            //Копируем файлы в директорию
            $DM_DEST = DirManagerCrop::cropAuto($cell->getCellId());
            $LOGGER->info('Dest dir: {}', $DM_DEST->relDirPath());

            //Копируем файлы в конечную директорию
            $success = copy($absPathBig, $DM_DEST->absFilePath(null, CropConst::TMP_FILE_BIG, CropConst::CROP_EXT));
            if (!$success) {
                $DM_DEST->removeDir();
                return PsUtil::raise('Cannot copy crop image');
            }
            $success = copy($absPathSmall, $DM_DEST->absFilePath(null, CropConst::TMP_FILE_SMALL, CropConst::CROP_EXT));
            if (!$success) {
                $DM_DEST->removeDir();
                return PsUtil::raise('Cannot copy thumbnail');
            }

            //Подтверждаем ячейку
            CropCellsManager::inst()->submitCell($cell->getCellId());

            //Временная директория теперь не нужна
            $DM_TEMP->removeDir();
            $DM_TEMP = null;

            //Если у нас крайняя ячейка - перестроим группу
            if (CropConst::CROPS_GROUP_CELLS == $cell->getX()) {
                $LOGGER->info('We should rebuild group №{}', $cell->getY());
                try {
                    CropGroupImgGenerator::makeGroup($cell->getY());
                } catch (Exception $e) {
                    $LOGGER->info('Group №{} rebuilding error: {}', $cell->getY(), $e->getTraceAsString());
                }
            }

            //Возвращаем ячейку
            return $cell; //---
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
            if ($DM_TEMP_CLEAR && $DM_TEMP) {
                $DM_TEMP_CLEAR = false;
                $DM_TEMP->removeDir();
            }
            /*
             * Логируем ошибку
             */
            $LOGGER->info('Crop processing error: {}', $ex->getMessage());
            /*
             * Снимаем дамп
             */
            ExceptionHandler::dumpError($ex);
            /*
             * Пробрасываем
             */
            throw $ex;
        }
    }

}