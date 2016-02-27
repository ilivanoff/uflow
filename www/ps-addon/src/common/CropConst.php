<?php

/**
 * Константы подсистемы
 *
 * @author azazello
 */
class CropConst extends CropConstJs {
    /**
     * Максимальное кол-во ячеек в группе (960/60)
     */

    const CROPS_GROUP_CELLS = 16;

    /**
     * Ширина группы ячеек
     */
    const CROPS_GROUP_WIDTH = 960;

    /**
     * Размер загруженного изображения
     */
    const CROP_SIZE_BIG = 240;
    const CROP_SIZE_SMALL = 60;

    /**
     * Названия временных файлов
     */
    const TMP_FILE_BIG = 'big';
    const TMP_FILE_SMALL = 'small';

    /**
     * Расширение файлов
     */
    const CROP_EXT = PsConst::EXT_PNG;

    /**
     * Порция загружаемых групп
     */
    const GROUPS_LOAD_PORTION = 24;

    /**
     * Эмоции
     */
    const EMOTION_JOY = 1;
    const EMOTION_SADNESS = 2;
    const EMOTION_ANGER = 3;
    const EMOTION_FEAR = 4;
    const EMOTION_DISGUST = 5;
    const EMOTION_BINGABONG = 6;

    /**
     * Метод возвращает название эмоции по её коду
     * 
     * @param int $code - код эмоции
     */
    public static function getEmotionName($code) {
        return strtolower(cut_string_start(PsUtil::getClassConstByValue(__CLASS__, 'EMOTION_', $code), 'EMOTION_'));
    }

}

?>
