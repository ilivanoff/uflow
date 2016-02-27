<?php

/**
 * Ячейка мозайки
 *
 * @author azazello
 */
class CropCell extends BaseDataStore {

    public static function instShort($cellId, $x, $y, $n) {
        return new CropCell(array('id_cell' => $cellId, 'x' => $x, 'y' => $y, 'n' => $n));
    }

    public function getCellId() {
        return PsCheck::int($this->id_cell);
    }

    public function getX() {
        return PsCheck::int($this->x);
    }

    public function getY() {
        return PsCheck::int($this->y);
    }

    public function getN() {
        return PsCheck::int($this->n);
    }

    public function getDtEvent() {
        return PsCheck::int($this->dt_event);
    }

    public function getText() {
        return $this->v_text;
    }

}

?>