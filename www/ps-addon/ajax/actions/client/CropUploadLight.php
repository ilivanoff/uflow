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
        return array('crop', 'text', 'em');
    }

    protected function executeImpl(ArrayAdapter $params) {

        if (!CropConst::ADD_CELL_ENABLED) {
            return 'Возможность добавления ячеек временно закрыта, приносим свои извинения.';
        }

        $text = $params->str('text');
        //Валидируем комментарий
        if (!$text) {
            return 'Вы не ввели текст';
        }
        $error = UserInputValidator::validateLongText($text);
        if ($error) {
            return $error;
        }
        $textLen = ps_strlen($text);
        if ($textLen > CropConst::CROP_MSG_MAX_LEN) {
            return PsStrings::replaceWithBraced('Максимальная длина текста: {}. Введено: {}.', CropConst::CROP_MSG_MAX_LEN, $textLen);
        }
        //$text = UserInputTools::safeLongText($text);

        CropUploaderLight::upload($params->str('crop'), $text, $params->int('em'));

        return new AjaxSuccess();
    }

}

?>