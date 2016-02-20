<?php

/**
 * Тестовые функции
 *
 * @author azazello
 */
class CropTests {

    private static $images = null; //---

    private static function images() {
        return is_array(self::$images) ? self::$images : self::$images = DirManager::inst()->getDirContent(DirManagerCrop::DIR_CROP_TEST, DirItemFilter::IMAGES);
    }

    /**
     * Метод возвращает случайное изображение больших размеров (как загружаемая картинка)
     * @return DirItem картинка стандартных загружаемых размеров
     */
    public static function randomCropBigImgDi() {
        $img = self::images()[array_rand(self::images())];
        return PsImgEditor::resize($img, CropConst::CROP_SIZE_BIG . 'x' . CropConst::CROP_SIZE_BIG);
    }

    /**
     * Метод возвращает случайное изображение маленьких размеров
     * @return DirItem картинка стандартных загружаемых размеров
     */
    public static function randomCropSmallImgDi() {
        $img = self::images()[array_rand(self::images())];
        return PsImgEditor::resize($img, CropConst::CROP_SIZE_SMALL . 'x' . CropConst::CROP_SIZE_SMALL);
    }

    /**
     * Метод возвращает ифыу64 случайного изображения
     * @return string картинка стандартных загружаемых размеров
     */
    public static function randomCropImgBase64() {
        $imagedata = file_get_contents(self::randomCropBigImgDi()->getAbsPath());
        $base64 = base64_encode($imagedata);
        return CropUploaderLight::DATA_IMG_PREFIX . $base64;
    }

    /**
     * Метод генерирует ячейку
     */
    public static function makeCropCell() {
        return CropUploaderLight::upload(self::randomCropImgBase64(), getRandomString(100, true, 10));
    }

    /**
     * Метод создаёт сразу несколько ячеек
     */
    public static function makeCropCells($count) {
        for ($i = 0; $i < PsCheck::positiveInt($count); $i++) {
            self::makeCropCell();
        }
    }

    /**
     * Метод удаляет всё - все ячейки, всю историю
     */
    public static function clean() {
        DirManagerCrop::cropsDir()->removeDir();
        DirManagerCrop::tempsDir()->removeDir();
        DirManagerCrop::groupsDir()->removeDir();
        PSDB::update('delete from crop_cell');
        PSDB::update('ALTER TABLE crop_cell AUTO_INCREMENT = 1');
    }

}

?>