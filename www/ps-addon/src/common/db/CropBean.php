<?php

/**
 * Класс для работы с БД
 *
 * @author azaz
 */
class CropBean extends BaseBean {

    /**
     * Метод привязывает ячейку в БД
     * 
     * @param string $temp - временное хранилище
     * @param string $text - текст ячейки
     */
    public function makeCell($temp, $text) {
        $cellId = null;

        PsLock::lockMethod(__CLASS__, __FUNCTION__);
        try {
            $cellId = PsCheck::positiveInt($this->insert('INSERT INTO crop_cell (dt_event, b_ok, v_temp, v_text) VALUES (unix_timestamp(), 0, ?, ?)', array($temp, $text)));

            $cellNum = PsCheck::notNegativeInt($this->getCnt('select count(1) as cnt from crop_cell') - 1);

            $x = 1 + $cellNum % CropConst::CROPS_GROUP_CELLS;
            $y = 1 + round(($cellNum - $x) / CropConst::CROPS_GROUP_CELLS);

            $this->update('update crop_cell set x=?, y=?, n=? where id_cell=?', array($x, $y, 1 + $cellNum, $cellId));
        } catch (Exception $e) {
            PsLock::unlock();
            throw $e; //---
        }

        PsLock::unlock();
        return $cellId; //---
    }

    /**
     * Метод подтверждает ячейку
     * 
     * @param int $cellId - код ячейки
     * @return type
     */
    public function submitCell($cellId) {
        return $this->update('UPDATE crop_cell set v_temp=null, b_ok=1 where b_ok=0 and id_cell=?', PsCheck::positiveInt($cellId));
    }

    /**
     * Метод загружает ячейки группы от левого края к правому
     * 
     * @param int $n - номер группы
     */
    public function getGroupCells($n) {
        return $this->getValues('select id_cell as value from crop_cell where y=? order by x desc', PsCheck::positiveInt($n));
    }

    /**
     * Метод загружает номер последней ячейки
     */
    public function getMaxY() {
        $y = $this->getValue('select max(y) as y from crop_cell');
        return PsCheck::isInt($y) ? 1 * $y : null; //---
    }

    /**
     * Метод загружает ячейки групп для показа
     */
    public function loadCells4Show($lastGr, $portion) {
        return $this->getArrayIndexedMulti('select id_cell, x, y from crop_cell where y<? and y>=? order by n desc', array($lastGr, $lastGr - $portion), 'y');
    }

    /** @return CropBean */
    public static function inst() {
        return parent::inst();
    }

}
