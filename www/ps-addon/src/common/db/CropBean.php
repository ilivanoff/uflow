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
     * @param string $temp - название директории временного хранилища, чтобы восстановить привязку в случае ошибки
     * @param string $text - текст ячейки
     * @return CropCell Ячейка
     */
    public function makeCell($temp, $text, $em) {
        PsLock::lockMethod(__CLASS__, __FUNCTION__);
        try {
            $cellId = PsCheck::positiveInt($this->insert('INSERT INTO crop_cell (dt_event, n_em, b_ok, v_temp, v_text) VALUES (unix_timestamp(), ?, 0, ?, ?)', array(PsCheck::int($em), $temp, $text)));

            $cellNum = PsCheck::notNegativeInt($this->getCnt('select count(1) as cnt from crop_cell') - 1);

            $x = PsCheck::int(1 + $cellNum % CropConst::CROPS_GROUP_CELLS);
            $y = PsCheck::int(1 + round(($cellNum - $x) / CropConst::CROPS_GROUP_CELLS));
            $n = PsCheck::int(1 + $cellNum);

            $this->update('update crop_cell set x=?, y=?, n=? where id_cell=?', array($x, $y, $n, $cellId));

            PsLock::unlock();

            return CropCell::instShort($cellId, $x, $y, $n); //---
        } catch (Exception $e) {
            PsLock::unlock();
            throw $e; //---
        }
    }

    /**
     * Метод подтверждает ячейку
     * 
     * @param int $cellId - код ячейки
     * @return bool - признак, привязана ли ячейка
     */
    public function submitCell($cellId) {
        return 1 == $this->update('UPDATE crop_cell set v_temp=null, b_ok=1 where b_ok=0 and id_cell=?', PsCheck::positiveInt($cellId));
    }

    /**
     * Метод загружает коды ячеек группы от левого края к правому
     * 
     * @param int $y - номер группы
     */
    public function getGroupCellIds($y) {
        return $this->getIds('select id_cell as id as value from crop_cell where y=? order by x desc', PsCheck::positiveInt($y));
    }

    /**
     * Метод загружает ячейки группы от левого края к правому
     * 
     * @param int $y - номер группы
     */
    public function getGroupCells($y) {
        return $this->getArray('select id_cell, x, y, v_text, dt_event from crop_cell where y=? order by x desc', PsCheck::positiveInt($y));
    }

    /**
     * Метод загружает номер последней ячейки
     */
    public function getMaxY() {
        $y = $this->getValue('select max(y) as y from crop_cell');
        return PsCheck::isInt($y) ? PsCheck::int($y) : null; //---
    }

    /**
     * Метод загружает ячейки групп для показа
     */
    public function loadCells4Show($lastGr, $portion) {
        return $this->getArrayIndexedMulti('select id_cell, x, y, v_text, dt_event from crop_cell where y<? and y>=? order by n desc', array($lastGr, $lastGr - $portion), 'y');
    }

    /**
     * Метод получает ячейку по её коду
     */
    public function getCell($cellId, $required = true) {
        return PsCheck::isInt($cellId) ? $this->getObject('select * from crop_cell where id_cell=?', array($cellId), CropCell::getClass(), null, $required) : ($required ? raise_error('Не передан код ячейки') : null);
    }

    /** @return CropBean */
    public static function inst() {
        return parent::inst();
    }

}
