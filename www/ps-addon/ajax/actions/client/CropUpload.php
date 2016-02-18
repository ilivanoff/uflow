<?php

class CropUpload extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array('imgo', 'imgc', 'imgf', 'file');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $file = $params->arr('file');
        $imgo = $params->str('imgo');
        $imgf = $params->str('imgf');
        $imgc = $params->str('imgc');

        CropUploader::upload($imgo, $imgf, $imgc, $file);

        return new AjaxSuccess();
    }

}

?>