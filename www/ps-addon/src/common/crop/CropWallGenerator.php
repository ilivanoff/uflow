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
                echo "<map id='cgr-$y'>";
                foreach ($cells as $cell) {
                    $idCell = $cell['id_cell'];
                    $xCell = round((CropConst::CROPS_GROUP_CELLS - $cell['x']) * CropConst::CROP_SIZE_SMALL);
                    $xCellEnd = $xCell + CropConst::CROP_SIZE_SMALL;
                    echo "<area data-c='$idCell' coords='$xCell, 0, $xCellEnd, 60' shape='rect' nohref='nohref'>";
                }
                echo '</map>';
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