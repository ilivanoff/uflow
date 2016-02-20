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
        $lastY = null;
        foreach (CropCellsManager::inst()->loadCells4Show($lastGr, 100) as $cell) {
            $idCell = $cell['id_cell'];
            $x = $cell['x'];
            $y = $cell['y'];
            if ($lastY != $y) {
                if ($lastY) {
                    echo '</div>';
                }
                echo "<div data-gr='$y'>";
                $lastY = $y;
            }
            echo PsHtml::img(array('src' => '/' . DirManagerCrop::DIR_CROP . '/' . $idCell . '/' . CropConst::TMP_FILE_SMALL . '.' . CropConst::CROP_EXT));
        }
        if ($lastY) {
            echo '</div>';
        }
    }

}

?>
