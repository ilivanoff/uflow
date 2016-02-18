<?php

/**
 * Класс отвечает за загрузку изображений в систему
 *
 * @author azaz
 */
class CropUploader {

    /**
     * 
     * @param string $imgo - оригинальное изображение
     * @param string $imgf - 
     * @param string $imgc
     * @param array $file
     */
    public static function upload($imgo, $imgf, $imgc, array $file) {

        $data = $params->str('imgc');
        $data = explode(',', $data, 2)[1];
        $unencoded = base64_decode($data);
        $im = imagecreatefromstring($unencoded);
        imagepng($im, DirItem::inst(PS_DIR_ADDON . '/crop', 'imgc', PsConst::EXT_PNG)->getAbsPath());
        imagedestroy($im);

        $data = $params->str('imgo');
        $data = explode(',', $data, 2)[1];
        $unencoded = base64_decode($data);
        $im = imagecreatefromstring($unencoded);
        imagejpeg($im, DirItem::inst(PS_DIR_ADDON . '/crop', 'imgo', PsConst::EXT_JPEG)->getAbsPath(), 70);
        imagepng($im, DirItem::inst(PS_DIR_ADDON . '/crop', 'imgo', PsConst::EXT_PNG)->getAbsPath());
        imagedestroy($im);

        $data = $params->str('imgf');
        $data = explode(',', $data, 2)[1];
        $unencoded = base64_decode($data);
        $im = imagecreatefromstring($unencoded);
        imagepng($im, DirItem::inst(PS_DIR_ADDON . '/crop', 'imgf', PsConst::EXT_PNG)->getAbsPath());
        imagedestroy($im);
    }

}
