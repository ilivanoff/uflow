<?php

/**
 * Класс отвечает за загрузку изображений в систему
 *
 * @author azaz
 */
class CropUploader {

    /** @var PsLoggerEmpty */
    private $LOGGER;

    /** @var DirManager */
    private $DIR_MANAGER_TMP;

    private function save($dataUrl, $name) {
        if ($dataUrl) {
            $dataUrl = explode(',', $dataUrl, 2)[1];
            $unencoded = base64_decode($dataUrl);
            $im = imagecreatefromstring($unencoded);
            //print_r(imagesx($im));
            //print_r(imagesy($im));
            imagepng($im, $this->DIR_MANAGER_TMP->absFilePath(null, $name, PsConst::EXT_PNG));
            imagedestroy($im);
        }
    }

    /**
     * Загрузка изображений на сервер
     * 
     * @param string $imgo - оригинальное изображение
     * @param string $imgf - изображение с фильтром (может и не быть указано)
     * @param string $imgc - обрезанное изображение
     * @param array $file  - информация о файле
     * @param type $text   - текст сообщения
     */
    public function uploadImpl($imgo, $imgf, $imgc, array $file, $text, array $crop) {
        $this->LOGGER->info('Dir: ' . $this->DIR_MANAGER_TMP->absDirPath());
        $this->LOGGER->info('imgo: ' . $imgo);
        $this->LOGGER->info('imgf: ' . $imgf);
        $this->LOGGER->info('imgc: ' . $imgc);
        $this->LOGGER->info('file: ' . print_r($file, true));
        $this->LOGGER->info('crop: ' . print_r($crop, true));
        $this->LOGGER->info('text: ' . $text);

        $this->save($imgo, 'imgo');
        $this->save($imgf, 'imgf');
        $this->save($imgc, 'imgc');
    }

    /**
     * Метод вызывается для загрузки изображения
     */
    public static function upload($imgo, $imgf, $imgc, array $file, $text, array $crop) {
        return (new CropUploader())->uploadImpl($imgo, $imgf, $imgc, $file, $text, $crop);
    }

    /**
     * Конструктор
     */
    private function __construct() {
        $this->LOGGER = PsLogger::inst(__CLASS__);
        $this->DIR_MANAGER_TMP = DirManagerCrop::cropTempDir();
    }

}
