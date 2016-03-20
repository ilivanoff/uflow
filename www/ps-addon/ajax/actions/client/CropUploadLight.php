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
        return array('crop', 'email', 'author', 'text', 'em', 'cap');
    }

    protected function executeImpl(ArrayAdapter $params) {

        if (!CropConst::ADD_CELL_ENABLED) {
            return 'Возможность добавления ячеек временно закрыта, приносим свои извинения.';
        }

        /*
         * КАПЧА
         */
        if (!PSreCAPTCHA::isValid($params->str('cap'))) {
            return 'Введённая капча невалидна';
        }

        /*
         * EMAIL
         */
        $email = $params->str('email');
        if (!PsCheck::isEmail($email)) {
            return 'Введён некорректный email';
        }
        //TODO - вынести на настройки
        if (ps_strlen($email) > 255) {
            return 'Введён некорректный email';
        }

        /*
         * AUTHOR
         */
        $author = $params->str('author');
        if (ps_strlen($author) > 255) {
            return 'Подпись не должна превышать 255 символов';
        }

        /*
         * ТЕКСТ
         */
        $text = normalize_string($params->str('text'));
        //Валидируем комментарий
        if (!$text) {
            return 'Вы не ввели текст';
        }
        $censure = PsCensure::parse($text);
        if ($censure) {
            return "Текст содержит нецензурную лексику: $censure";
        }
        $textLen = ps_strlen($text);
        if ($textLen > CropConst::CROP_MSG_MAX_LEN) {
            return PsStrings::replaceWithBraced('Максимальная длина текста: {}. Введено: {}.', CropConst::CROP_MSG_MAX_LEN, $textLen);
        }
        //$text = UserInputTools::safeLongText($text);

        /*
         * Загружаем ячейку
         */
        $cell = CropUploaderLight::upload($params->str('crop'), $email, $author, $text, false, $params->int('em'));

        /*
         * Строим страницу с ответом
         */
        $added = PSSmarty::template('crop/added.tpl', array('cell' => $cell))->fetch();

        /*
         * Возвращаем ответ
         */
        return new AjaxSuccess(array('page' => $added));
    }

}

?>