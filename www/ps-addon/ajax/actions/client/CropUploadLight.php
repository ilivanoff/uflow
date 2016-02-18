<?php

/**
 * Класс для быстрой загрузки картинки
 *
 * @author azaz
 */
class CropUploadLight extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function isCheckActivity() {
        return true;
    }

    protected function getRequiredParamKeys() {
        return array('crop', 'text');
    }

    protected function executeImpl(ArrayAdapter $params) {
        $text = $params->str('text');
        $crop = $params->str('crop');
        CropUploaderLight::upload($crop, $text);
        return new AjaxSuccess();
    }

}

?>