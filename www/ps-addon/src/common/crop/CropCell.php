<?php

/**
 * Ячейка мозайки
 *
 * @author azazello
 */
class CropCell extends BaseDataStore {

    public static function instShort($cellId, $x, $y, $n, $email) {
        return new CropCell(array('id_cell' => $cellId, 'x' => $x, 'y' => $y, 'n' => $n, 'v_mail' => $email));
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

    public function getMail() {
        return $this->v_mail;
    }

    public function getAuthor() {
        return $this->v_author;
    }

    public function hasAuthor() {
        return PsCheck::isNotEmptyString($this->v_author);
    }

    public function getText() {
        return $this->v_text;
    }

    public function isBanned() {
        return 1 == $this->b_ban;
    }

    public function isHtml() {
        return 1 == $this->b_html;
    }

    public function getAuthor4Show() {
        return $this->hasAuthor() ? html_4show($this->getAuthor()) : '';
    }

    public function getText4Show() {
        return $this->isHtml() ? $this->getText() : html_4show($this->getText());
    }

    public function existsImgBig() {
        return DirManagerCrop::imgExists($this->id_cell, CropConst::TMP_FILE_BIG);
    }

    public function existsImgSmall() {
        return DirManagerCrop::imgExists($this->id_cell, CropConst::TMP_FILE_SMALL);
    }

    public function relImgBig() {
        return DirManagerCrop::cropDiBig($this->id_cell)->getRelPath();
    }

    public function relImgSmall() {
        return DirManagerCrop::cropDiSmall($this->id_cell)->getRelPath();
    }

}

?>
