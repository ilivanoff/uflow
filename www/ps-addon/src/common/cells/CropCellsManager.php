<?php

/**
 * Description of CropCellsManager
 *
 * @author azaz
 */
class CropCellsManager extends AbstractSingleton {

    /**
     * Метод привязывает ячейку
     * 
     * @param string $tempStorage - название директории временного хранилища, чтобы восстановить привязку в случае ошибки
     * @param string $text - текст ячейки
     * @return type
     */
    public function bindCell($tempStorage, $text) {
        return CropBean::inst()->makeCell($tempStorage, $text);
    }

    /**
     * Метод подтверждает ячейку
     * 
     * @param int $cellId - код ячейки
     * @return bool - признак, привязана ли ячейка
     */
    public function submitCell($cellId) {
        return 1 == CropBean::inst()->submitCell($cellId);
    }

    /** @return CropCellsManager */
    public static function inst() {
        return parent::inst();
    }

}
