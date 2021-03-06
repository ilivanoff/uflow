<?php

/**
 * Проверка рекапчи
 * TODO - вынести на общие глобальные настроки
 *
 * @author azazello
 */
class PSreCAPTCHA {

    public static function isValid($gRec) {
        if (PsCheck::isNotEmptyString($gRec)) {
            //Подключаем библиотеку
            PsCropLibs::inst()->reCAPTCHA();

            $recaptcha = new \ReCaptcha\ReCaptcha(CROP_CAPTCHA_PRIVATE);
            return $recaptcha->verify($gRec, $_SERVER['REMOTE_ADDR'])->isSuccess();
        }
        return false; //---
    }

}

?>
