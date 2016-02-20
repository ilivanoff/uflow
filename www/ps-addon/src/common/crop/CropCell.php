<?php

/**
 * Ячейка мозайки
 *
 * @author azazello
 */
class CropCell {

    private $cellId;
    private $x;
    private $y;
    private $n;

    public function __construct($cellId, $x, $y, $n) {
        $this->cellId = $cellId;
        $this->x = $x;
        $this->y = $y;
        $this->n = $n;
    }

    public function getCellId() {
        return $this->cellId;
    }

    public function getX() {
        return $this->x;
    }

    public function getY() {
        return $this->y;
    }

    public function getN() {
        return $this->n;
    }

}

?>
