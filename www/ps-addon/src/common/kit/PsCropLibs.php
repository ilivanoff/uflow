<?php

/**
 * Кастомные библиотеки
 *
 * @author azazello
 */
class PsCropLibs extends PsLibs {

    /**
     * Библиотека для работы с базой
     * 
     * @link http://adodb.sourceforge.net
     */
    public function reCAPTCHA() {
        if ($this->isAlreadyIncluded(__FUNCTION__)) {
            return; //---
        }

        require_once $this->PROJ_LIB_DIR . 'recaptcha-1.1.2/src/autoload.php';
    }

    /** @return PsCropLibs */
    public static function inst() {
        return parent::inst();
    }

}

?>
