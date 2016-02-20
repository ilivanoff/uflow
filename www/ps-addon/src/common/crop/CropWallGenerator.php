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

}

?>
