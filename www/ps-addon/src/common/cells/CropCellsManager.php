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
     */
    public function bindCell($tempStorage, $text) {
        $cellId = CropBean::inst()->makeCell($tempStorage, $text);
        return $cellId; //---
    }

    public function subitCell($cellId) {
        //TODO
    }

    /** @return CropCellsManager */
    public static function inst() {
        return parent::inst();
    }

}
