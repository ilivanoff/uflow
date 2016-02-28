<?php

/**
 * Description of CropCellsManager
 *
 * @author azaz
 */
class CropCellsManager extends AbstractSingleton {

    /**
     * Метод загружает ячейки групп для показа
     */
    public function loadCells4Show($lastGr = null, $portion = CropConst::GROUPS_LOAD_PORTION) {
        if (PsCheck::isInt($lastGr)) {
            $lastGr = 1 * $lastGr;
        } else {
            $maxY = CropBean::inst()->getMaxY();
            $lastGr = PsCheck::isInt($maxY) ? 1 + $maxY : null;
        }
        return is_null($lastGr) ? array() : CropBean::inst()->loadCells4Show($lastGr, $portion);
    }

    /**
     * Метод загружает номер последней ячейки
     */
    public function getMaxY() {
        return CropBean::inst()->getMaxY();
    }

    /** @return CropCellsManager */
    public static function inst() {
        return parent::inst();
    }

}
