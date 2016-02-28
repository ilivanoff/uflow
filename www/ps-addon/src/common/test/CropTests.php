<?php

/**
 * Тестовые функции
 *
 * @author azazello
 */
class CropTests {

    private static $images = null; //---
    /** @var DirItem */
    private static $image = null; //---

    private static function images() {
        PsDefines::assertProductionOff(__CLASS__);
        return is_array(self::$images) ? self::$images : self::$images = DirManager::inst()->getDirContent(DirManagerCrop::DIR_CROP_TEST, DirItemFilter::IMAGES);
    }

    /**
     * Метод возвращает произвольную картинку
     * @return DirItem не изменённая картинка
     */
    private static function randomImg() {
        do {
            $img = self::images()[array_rand(self::images())];
            if (!self::$image || !self::$image->equals($img)) {
                return self::$image = $img; //---
            }
        } while (true);
    }

    /**
     * Метод возвращает случайное изображение больших размеров (как загружаемая картинка)
     * @return DirItem картинка стандартных загружаемых размеров
     */
    public static function randomCropBigImgDi() {
        return PsImgEditor::resize(self::randomImg(), CropConst::CROP_SIZE_BIG . 'x' . CropConst::CROP_SIZE_BIG);
    }

    /**
     * Метод возвращает случайное изображение маленьких размеров
     * @return DirItem картинка стандартных загружаемых размеров
     */
    public static function randomCropSmallImgDi() {
        return PsImgEditor::resize(self::randomImg(), CropConst::CROP_SIZE_SMALL . 'x' . CropConst::CROP_SIZE_SMALL);
    }

    /**
     * Метод возвращает ифыу64 случайного изображения
     * @return string картинка стандартных загружаемых размеров
     */
    public static function randomCropBigImgBase64() {
        $imagedata = file_get_contents(self::randomCropBigImgDi()->getAbsPath());
        $base64 = base64_encode($imagedata);
        return CropUploaderLight::DATA_IMG_PREFIX . $base64;
    }

    /**
     * Случайный код эмоции
     */
    public static function randomEmotionCode() {
        $emotions = PsUtil::getClassConsts('CropConst', 'EMOTION_');
        return $emotions[array_rand($emotions)];
    }

    /**
     * Метод генерирует ячейку
     */
    public static function makeCropCell() {
        return CropUploaderLight::upload(self::randomCropBigImgBase64(), getRandomString(CropConst::CROP_MSG_MAX_LEN, true, 10), CropTests::randomEmotionCode());
    }

    /**
     * Метод создаёт сразу несколько ячеек
     */
    public static function makeCropCells($count) {
        for ($i = 0; $i < PsCheck::int($count); $i++) {
            self::makeCropCell();
        }
    }

    /**
     * Метод удаляет всё - все ячейки, всю историю
     */
    public static function clean() {
        CropCache::GROUPS()->clean();
        DirManagerCrop::cropsDir()->removeDir();
        DirManagerCrop::tempsDir()->removeDir();
        DirManagerCrop::groupsDir()->removeDir();
        PSDB::update('delete from crop_cell');
        PSDB::update('ALTER TABLE crop_cell AUTO_INCREMENT = 1');
    }

}

?>