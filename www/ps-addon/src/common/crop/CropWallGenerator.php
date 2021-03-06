<?php

/**
 * Класс генерации стены
 */
class CropWallGenerator {

    public static function generate() {
        echo '<div class="gr">';
        for ($i = 0; $i < rand(round(CropConst::CROPS_GROUP_CELLS / 2), CropConst::CROPS_GROUP_CELLS - 1); $i++) {
            echo PsHtml::img(array('src' => CropTests::randomCropSmallImgDi()));
        }
        echo '</div>';

        for ($i = 0; $i < 20; $i++) {
            echo '<div class="gr">';
            for ($j = 0; $j < CropConst::CROPS_GROUP_CELLS; $j++) {
                echo PsHtml::img(array('src' => CropTests::randomCropSmallImgDi()));
            }
            echo '</div>';
        }
    }

    /**
     * Версия кеширования
     */

    const CACHE_VERSION = '1.b';

    /**
     * Метод генерирует стену
     * 
     * @param int|null $topY - номер последней группы
     */
    public static function build($topY = null) {
        //self::generate();
        //return;
        PsProfiler::inst(__CLASS__)->start('Build');
        self::buildWallCache($topY);
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

        $bottomY = max(array(1 + $topY - CropConst::GROUPS_LOAD_PORTION, 1));

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
            $banned = 1 == $cell['b_ban'];
            $ishtml = 1 == $cell['b_html'];

            $content .= 'cells[' . $cell['id_cell'] . ']=' . json_encode(array(
                        'b' => $banned,
                        'd' => $cell['dt_event'],
                        'a' => $banned ? '' : $cell['v_author'],
                        't' => $banned ? '' : $cell['v_text'],
                        'h' => $ishtml
                    )) . ';';
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
            $banned = 1 == $cell['b_ban'];
            $imgParams = array();
            if ($banned) {
                //Если ячейка забанена - положем в данные ещё код ячейки, чтобы можно было его определить в js
                $imgParams['src'] = DirManagerCrop::banDiSmall($cell['id_cell'])->getRelPath();
                $imgParams['data'] = array('c' => $cell['id_cell']);
            } else {
                $imgParams['src'] = DirManagerCrop::cropRelSmall($cell['id_cell']);
            }
            $content .= PsHtml::img($imgParams);
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