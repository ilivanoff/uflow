<?php

/**
 * Класс генерации стены
 */
class CropWallGenerator {

    public static function generate() {
        for ($i = 0; $i < rand(2000, 3000); $i++) {
            echo PsHtml::img(array('src' => CropTests::randomCropSmallImgDi()));
        }
    }

    /**
     * Метод генерирует стену
     */
    public static function build($lastGr = null) {
        foreach (CropCellsManager::inst()->loadCells4Show($lastGr) as $y => $cells) {
            echo "<div data-gr='$y'>";

            $groupDi = DirManagerCrop::groupFile($y);
            if ($groupDi->isFile()) {
                echo PsHtml::img(array('usemap' => '#cgr-' . $y, 'src' => $groupDi));
            } else {
                foreach ($cells as $cell) {
                    echo PsHtml::img(array('src' => '/' . DirManagerCrop::DIR_CROP . '/' . $cell['id_cell'] . '/' . CropConst::TMP_FILE_SMALL . '.' . CropConst::CROP_EXT));
                }
            }

            echo '</div>';
        }
    }

}

?>