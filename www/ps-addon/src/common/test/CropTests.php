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
     * Метод возвращает случайное изображение
     * @return DirItem картинка стандартных загружаемых размеров
     */
    public static function randomCropImgDi() {
        $img = self::images()[array_rand(self::images())];
        return PsImgEditor::resize($img, CropUploaderLight::CROP_SIZE_BIG . 'x' . CropUploaderLight::CROP_SIZE_BIG);
    }

    /**
     * Метод возвращает ифыу64 случайного изображения
     * @return string картинка стандартных загружаемых размеров
     */
    public static function randomCropImgBase64() {
        $imagedata = file_get_contents(self::randomCropImgDi()->getAbsPath());
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

}

?>