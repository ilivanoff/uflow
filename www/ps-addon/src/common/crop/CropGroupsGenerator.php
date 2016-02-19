<?php

/**
 * Класс склеивает изображения в группу
 *
 * @author azazello
 */
class CropGroupsGenerator {

    /**
     * Коды ячеек, по которым будет построена группа.
     * Не должно превышать кол-во CropConst::CROPS_GROUP_CELLS.
     * 
     * @param array $cells - коды ячеек
     */
    public static function makeGroup(array $cells) {
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
        //Сделаем фон прозрачным
        $black = imagecolorallocate($group_image, 0, 0, 0);
        imagecolortransparent($group_image, $black);
        //Копируем ячейки
        $cells = array_reverse($cells);
        $cellNum = 0;
        foreach ($cells as $cellId) {
            $cellImgAbs = DirManagerCrop::cropsDir()->absFilePath($cellId, CropConst::TMP_FILE_SMALL, CropConst::CROP_EXT);
            if (!PsImg::isImg($cellImgAbs)) {
                @imagedestroy($group_image);
                return PsUtil::raise('Cannot build group. Cannot find image for cell {}.', $cellId);
            }
            $imSmall = imagecreatefrompng($cellImgAbs);
            //TODO - проверить размеры полученного файла, а также - что всё скопировалось
            imagecopy($group_image, $imSmall, $cellNum * CropConst::CROP_SIZE_SMALL, 0, 0, 0, CropConst::CROP_SIZE_SMALL, CropConst::CROP_SIZE_SMALL);
            @imagedestroy($imSmall);
            //Увеличиваем счётчик
            ++$cellNum;
        }

        //Сохраним полученное с клиента изображение
        $success = imagepng($group_image, DirManager::inst(null, DirManagerCrop::DIR_GROUP)->absFilePath(null, 'gr', CropConst::CROP_EXT));
        check_condition($success, 'Cannot save cropped group');
        @imagedestroy($group_image);
        $group_image = null;
    }

}

?>
