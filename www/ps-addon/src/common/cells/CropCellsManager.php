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

    /**
     * Метод загружает ячейки групп для показа
     */
    public function loadCells4Show($lastGr = null, $portion = CropConst::GROUPS_LOAD_PORTION) {
        $lastGr = PsCheck::isInt($lastGr) ? 1 * $lastGr : 1 + CropBean::inst()->getMaxY();
        return is_null($lastGr) ? array() : CropBean::inst()->loadCells4Show($lastGr, $portion);
    }

    /** @return CropCellsManager */
    public static function inst() {
        return parent::inst();
    }

}
