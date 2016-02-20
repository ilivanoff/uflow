<?php

/**
 * Класс склеивает изображения в группу
 *
 * @author azazello
 */
class CropGroupsGenerator {

    public static function makeGroup($groupNum) {
        //Создаём картинку
        return self::makeGroupImpl($groupNum, CropBean::inst()->getGroupCells($groupNum));
    }

    /**
     * Коды ячеек, по которым будет построена группа.
     * Не должно превышать кол-во CropConst::CROPS_GROUP_CELLS.
     * 
     * @param array $cells - коды ячеек
     */
    private static function makeGroupImpl($groupNum, array $cells) {
        $cellsCnt = count($cells);
        if ($cellsCnt == 0) {
            return null; //---
        }
        if ($cellsCnt > CropConst::CROPS_GROUP_CELLS) {
            return PsUtil::raise('Invalid cells count for group. Given: {}. Maximum: {}.', $cellsCnt, CropConst::CROPS_GROUP_CELLS);
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
                @imagedestroy($group_image);
                return PsUtil::raise('Cannot build group. Cannot find image for cell {}.', $cellId);
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
    }

}

?>
