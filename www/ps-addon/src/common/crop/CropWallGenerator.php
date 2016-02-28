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
     * Версия кеширования
     */

    const CACHE_VERSION = '1a';

    /**
     * Метод генерирует стену
     * 
     * @param int|null $topY - номер последней группы
     */
    public static function build($topY = null) {
        $useCache = true;
        PsProfiler::inst(__CLASS__)->start('Build ' . ($useCache ? 'cached' : 'direct'));
        if ($useCache) {
            self::buildWallCache($topY);
        } else {
            self::buildWallDirect($topY);
        }
        PsProfiler::inst(__CLASS__)->stop();
    }

    /**
     * Метод генерирует стену с использованием кеша
     * 
     * @param int|null $topY - номер последней группы
     */
    private static function buildWallCache($topY = null) {
        $topY = PsCheck::isInt($topY) ? PsCheck::int($topY) - 1 : CropBean::inst()->getMaxY();

        if (!PsCheck::isInt($topY) || $topY < 1) {
            return; //---
        }

        $bottomY = max(array(1 + $topY - CropConst::CROPS_GROUP_CELLS, 1));

        for ($y = $topY; $y >= $bottomY; --$y) {
            echo "<div class='gr' data-gr='$y'>";

            echo "<div class='crn l'>$y</div>";

            echo self::buildWallGroup($y);

            //Справа не показываем номер ячейки, так как там - навигация
            //echo "<div class='crn r'>$y</div>";

            echo '</div>';
        }
    }

    /**
     * Метод генерирует стену напрямую, без кеша
     * 
     * @param int|null $topY - номер последней группы
     */
    private static function buildWallDirect($lastGr = null) {
        foreach (CropCellsManager::inst()->loadCells4Show($lastGr) as $y => $cells) {
            $groupDi = DirManagerCrop::groupFile($y);
            $groupIsFile = $groupDi->isFile();

            echo "<div class='gr' data-gr='$y'>";

            echo "<div class='crn l'>$y</div>";

            if ($groupIsFile) {
                $grId = 'cgr-' . $y;
                echo PsHtml::img(array('usemap' => '#' . $grId, 'src' => $groupDi));
                echo "<map id='$grId' name='$grId'>";
                foreach ($cells as $cell) {
                    $idCell = $cell['id_cell'];
                    $xCell = round((CropConst::CROPS_GROUP_CELLS - $cell['x']) * CropConst::CROP_SIZE_SMALL);
                    $xCellEnd = $xCell + CropConst::CROP_SIZE_SMALL;
                    echo "<area data-c='$idCell' coords='$xCell, 0, $xCellEnd, 60' shape='rect' nohref='nohref'>";
                }
                echo '</map>';
            } else {
                foreach ($cells as $cell) {
                    //Не передаём данные, так как код картинки возмём из пути к ней
                    echo PsHtml::img(array(/* 'data' => array('c' => $cell['id_cell']), */'src' => '/' . DirManagerCrop::DIR_CROP . '/' . $cell['id_cell'] . '/' . CropConst::TMP_FILE_SMALL . '.' . CropConst::CROP_EXT));
                }
            }

            echo '<script>';
            echo 'var cells = window["cells"] || {};';
            foreach ($cells as $cell) {
                echo 'cells[' . $cell['id_cell'] . ']=' . json_encode(array(/* 'x' => $cell['x'], 'y' => $cell['y'], */'t' => $cell['v_text'], 'd' => $cell['dt_event'])) . ';';
            }
            echo '</script>';

            //Справа не показываем номер ячейки, так как там - навигация
            //echo "<div class='crn r'>$y</div>";

            echo '</div>';
        }
    }

    /**
     * Метод стоит ing+ing+ing+... картинок группы.
     * 
     * @param array $cells - ячейки
     */
    private static function buildWallGroup($y) {
        $hasGroupImg = DirManagerCrop::groupFile($y)->isFile();
        $cached = $hasGroupImg ? CropCache::GROUPS()->getFromCache("$y", null, self::CACHE_VERSION) : null;
        if ($cached) {
            return $cached; //---
        }

        $cells = CropBean::inst()->getGroupCells($y);

        $groupFinished = count($cells) == CropConst::CROPS_GROUP_CELLS;

        if ($groupFinished && $hasGroupImg) {
            return CropCache::GROUPS()->saveToCache(self::makeGroupMap($y, $cells) . self::makeGroupScript($cells), "$y", self::CACHE_VERSION);
        }

        return self::makeGroupImages($y, $cells) . self::makeGroupScript($cells);
    }

    /**
     * Метод стоит script с данными для ячеек, доступными из js.
     * 
     * @param array $cells - ячейки
     */
    private static function makeGroupScript(array $cells) {
        $content = '';
        foreach ($cells as $cell) {
            $content .= 'cells[' . $cell['id_cell'] . ']=' . json_encode(array(/* 'x' => $cell['x'], 'y' => $cell['y'], */'t' => $cell['v_text'], 'd' => $cell['dt_event'])) . ';';
        }
        return PsHtml::linkJs(null, $content);
    }

    /**
     * Метод стоит ing+map для отслеживания наведения на ячейку.
     * 
     * @param array $cells - ячейки
     */
    private static function makeGroupMap($y, array $cells) {
        $content = '';
        $grId = 'cgr-' . $y;
        $content .= PsHtml::img(array('usemap' => '#' . $grId, 'src' => DirManagerCrop::groupFile($y)));
        $content .= "<map id='$grId' name='$grId'>";
        foreach ($cells as $cell) {
            $idCell = $cell['id_cell'];
            $xCell = round((CropConst::CROPS_GROUP_CELLS - $cell['x']) * CropConst::CROP_SIZE_SMALL);
            $xCellEnd = $xCell + CropConst::CROP_SIZE_SMALL;
            $content .= "<area data-c='$idCell' coords='$xCell, 0, $xCellEnd, 60' shape='rect' nohref='nohref'>";
        }
        $content .= '</map>';
        return $content;
    }

    /**
     * Метод стоит ing+ing+ing+... картинок группы.
     * 
     * @param array $cells - ячейки
     */
    private static function makeGroupImages($y, array $cells) {
        $content = '';
        foreach ($cells as $cell) {
            $content .= PsHtml::img(array(/* 'data' => array('c' => $cell['id_cell']), */'src' => '/' . DirManagerCrop::DIR_CROP . '/' . $cell['id_cell'] . '/' . CropConst::TMP_FILE_SMALL . '.' . CropConst::CROP_EXT));
        }
        return $content;
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