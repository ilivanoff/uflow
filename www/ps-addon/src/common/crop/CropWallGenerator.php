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
        $groupY = null;
        foreach (CropCellsManager::inst()->loadCells4Show($lastGr) as $cell) {
            $idCell = $cell['id_cell'];
            $x = $cell['x'];
            $y = $cell['y'];
            if ($lastY != $y) {
                $groupY = null;
                if ($lastY) {
                    echo '</div>';
                }
                echo "<div data-gr='$y'>";
                $lastY = $y;
            }
            if ($groupY == $y) {
                //Подключена группа
                continue; //---
            }
            $groupDi = DirManagerCrop::groupFile($y);
            if ($groupDi->isFile()) {
                $groupY = $y;
                echo PsHtml::img(array('usemap' => '#cgr-' . $y, 'src' => $groupDi));
            } else {
                echo PsHtml::img(array('src' => '/' . DirManagerCrop::DIR_CROP . '/' . $idCell . '/' . CropConst::TMP_FILE_SMALL . '.' . CropConst::CROP_EXT));
            }
        }
        if ($lastY) {
            echo '</div>';
        }
    }

}

?>
