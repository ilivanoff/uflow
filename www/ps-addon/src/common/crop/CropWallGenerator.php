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
     * Кнопка добавления публикации
     */
    private static function addButton() {
        return "<a href='/index.php?page=img'><img src='/i/puzzle.png'></a>";
    }

    /**
     * Метод генерирует стену
     * TODO - 
     */
    public static function build($lastGr = null) {
        $add = true;
        foreach (CropCellsManager::inst()->loadCells4Show($lastGr) as $y => $cells) {
            $groupDi = DirManagerCrop::groupFile($y);
            $groupIsFile = $groupDi->isFile();

            if (!$add && ($groupIsFile || count($cells) == CropConst::CROPS_GROUP_CELLS)) {
                $add = true;
                echo '<div>' . self::addButton() . '</div>';
            }

            echo "<div data-gr='$y'>";

            if ($groupIsFile) {
                $grId = 'cgr-' . $y;
                echo PsHtml::img(array('usemap' => '#' . $grId, 'src' => $groupDi));
                echo "<map id='$grId'>";
                foreach ($cells as $cell) {
                    $idCell = $cell['id_cell'];
                    $xCell = round((CropConst::CROPS_GROUP_CELLS - $cell['x']) * CropConst::CROP_SIZE_SMALL);
                    $xCellEnd = $xCell + CropConst::CROP_SIZE_SMALL;
                    echo "<area data-c='$idCell' coords='$xCell, 0, $xCellEnd, 60' shape='rect' nohref='nohref'>";
                }
                echo '</map>';
            } else {
                if (!$add) {
                    $add = true;
                    echo self::addButton();
                }
                foreach ($cells as $cell) {
                    //Не передаём данные, так как код картинки возмём из пути к ней
                    echo PsHtml::img(array(/* 'data' => array('c' => $cell['id_cell']), */'src' => '/' . DirManagerCrop::DIR_CROP . '/' . $cell['id_cell'] . '/' . CropConst::TMP_FILE_SMALL . '.' . CropConst::CROP_EXT));
                }
            }

            echo '</div>';
        }

        if (!$add) {
            $add = true;
            echo '<div>' . self::addButton() . '</div>';
        }
    }

    /**
     * Метод загружает группы в строку
     * 
     * @param int $lastGr - последняя загруженная группа
     */
    public static function buildToString($lastGr = null) {
        return ContentHelper::getContent(__CLASS__ . '::build', null, array($lastGr));
    }

}

?>