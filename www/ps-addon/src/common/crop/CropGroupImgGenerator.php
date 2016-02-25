<?php

/**
 * Класс склеивает изображения группы в одно
 *
 * @author azazello
 */
class CropGroupImgGenerator {

    /**
     * Метод создаёт все группы.
     * 
     * @param bool $forceRebuild - признак ознает, что группа будет перестроена, даже если существует
     */
    public static function makeGroups($forceRebuild = false) {
        $maxY = CropCellsManager::inst()->getMaxY();
        if (PsCheck::isInt($maxY)) {
            for ($y = $maxY; $y >= 1; --$y) {
                $groupDi = DirManagerCrop::groupFile($y);
                $exists = $groupDi->isFile();
                $made = false;
                if ($forceRebuild || !$exists) {
                    $made = self::makeGroup($y);
                }
                //Если у нас перестроение и мы не создали группу - удалим файл, вдруг он существовал
                if ($forceRebuild && !$made) {
                    $groupDi->remove();
                }
            }
        }
    }

    /**
     * Метод создаёт группу
     * 
     * @param int $y - номер группы
     * @return type
     */
    public static function makeGroup($y) {
        //Создаём картинку
        return self::makeGroupImpl($y, CropBean::inst()->getGroupCells($y));
    }

    /**
     * Коды ячеек, по которым будет построена группа.
     * Группа будет построена только в том случае, когда кол-во ячеек равно CropConst::CROPS_GROUP_CELLS.
     * 
     * @param array $cells - коды ячеек
     */
    private static function makeGroupImpl($groupNum, array $cells) {
        $cellsCnt = count($cells);
        if ($cellsCnt != CropConst::CROPS_GROUP_CELLS) {
            return false; //---
        }
        //Сохраним уменьшенное изображение
        $group_image = imagecreatetruecolor(round($cellsCnt * CropConst::CROP_SIZE_SMALL), CropConst::CROP_SIZE_SMALL);
        check_condition($group_image, 'Cannot image create true color for group');
        //Сделаем фон белым
        imagefill($group_image, 0, 0, imagecolorallocate($group_image, 255, 255, 255));
        //Копируем ячейки
        $cellNum = 0;
        foreach ($cells as $cellId) {
            $cellImgAbs = DirManagerCrop::cropsDir()->absFilePath($cellId, CropConst::TMP_FILE_SMALL, CropConst::CROP_EXT);
            if (!PsImg::isImg($cellImgAbs)) {
                /*
                  @imagedestroy($group_image);
                  return PsUtil::raise('Cannot build group. Cannot find image for cell {}.', $cellId);
                 */
                //Просто пропускаем в мозайке
                continue; //----
            }
            $imSmall = imagecreatefrompng($cellImgAbs);
            //Проверим размеры загруженной картинки
            check_condition($imSmall, 'Cannot image create from png for cell ' . $cellId);
            //Получим параметры изображения
            $w = imagesx($imSmall);
            $h = imagesy($imSmall);
            //Проверим размеры изображения
            if ($w != CropConst::CROP_SIZE_SMALL || $w != $h) {
                return PsUtil::raise('Invalid size of crop cell {}: {}x{}', $cellId, $w, $h);
            }
            //Копируем ячейку в группу
            $success = imagecopy($group_image, $imSmall, $cellNum * CropConst::CROP_SIZE_SMALL, 0, 0, 0, CropConst::CROP_SIZE_SMALL, CropConst::CROP_SIZE_SMALL);
            check_condition($success, "Cannot copy cell $cellId to group img");

            //Уничтожаем картинку
            @imagedestroy($imSmall);
            //Увеличиваем счётчик
            ++$cellNum;
        }

        //Сохраним полученное с клиента изображение
        $success = imagepng($group_image, DirManager::inst(null, DirManagerCrop::DIR_GROUP)->absFilePath(null, $groupNum, CropConst::CROP_EXT));
        check_condition($success, 'Cannot save cropped group');

        //Уничтожаем картинку
        @imagedestroy($group_image);
        $group_image = null;

        return true; //---
    }

}

?>
