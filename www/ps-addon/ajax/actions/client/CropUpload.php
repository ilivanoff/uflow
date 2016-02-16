<?php

class CropUpload extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array('imgc', 'imgo', 'imgc');
    }

    protected function executeImpl(ArrayAdapter $params) {
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

        return new AjaxSuccess();
    }

}

?>