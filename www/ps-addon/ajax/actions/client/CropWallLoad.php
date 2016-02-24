<?php

/**
 * Метод загружает стену с собщениями
 *
 * @author azaz
 */
class CropWallLoad extends AbstractAjaxAction {

    protected function getAuthType() {
        return AuthManager::AUTH_TYPE_NO_MATTER;
    }

    protected function isCheckActivity() {
        return false;
    }

    protected function getRequiredParamKeys() {
        return array('y');
    }

    protected function executeImpl(ArrayAdapter $params) {
        return new AjaxSuccess(CropWallGenerator::buildToString($params->int('y')));
    }

}

?>