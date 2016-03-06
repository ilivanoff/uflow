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
     * @param string $email - электронный адрес
     * @param string $text - текст ячейки
     * @param int $em - код эмоции
     * @return CropCell Ячейка
     */
    public function makeCell($temp, $email, $text, $em) {
        PsLock::lockMethod(__CLASS__, __FUNCTION__);
        try {
            $cellId = PsCheck::positiveInt($this->insert('INSERT INTO crop_cell (dt_event, n_em, b_ok, b_ban, v_temp, v_mail, v_text) VALUES (unix_timestamp(), ?, 0, 0, ?, ?, ?)', array(PsCheck::int($em), $temp, PsCheck::email($email), PsCheck::notEmptyString($text))));

            $cellNum = PsCheck::notNegativeInt($this->getCnt('select count(1) as cnt from crop_cell') - 1);

            $x = PsCheck::int(1 + $cellNum % CropConst::CROPS_GROUP_CELLS);
            $y = PsCheck::int(1 + round(($cellNum - $x) / CropConst::CROPS_GROUP_CELLS));
            $n = PsCheck::int(1 + $cellNum);

            $this->update('update crop_cell set x=?, y=?, n=? where id_cell=?', array($x, $y, $n, $cellId));

            PsLock::unlock();

            return CropCell::instShort($cellId, $x, $y, $n, $email); //---
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
     * Метод банит ячейку
     * 
     * @param int $cellId - код ячейки
     * @return bool - признак, забанена ли ячейка
     */
    public function banCell($cellId) {
        return 1 == $this->update('UPDATE crop_cell set b_ban=1 where b_ban=0 and id_cell=?', PsCheck::positiveInt($cellId));
    }

    /**
     * Метод разбанит ячейку
     * 
     * @param int $cellId - код ячейки
     * @return bool - признак, разбанена ли ячейка
     */
    public function unbanCell($cellId) {
        return 1 == $this->update('UPDATE crop_cell set b_ban=0 where b_ban=1 and id_cell=?', PsCheck::positiveInt($cellId));
    }

    /**
     * Метод загружает коды ячеек группы от левого края к правому
     * 
     * @param int $y - номер группы
     */
    public function getGroupCellIds($y) {
        return $this->getIds('select id_cell as id from crop_cell where y=? order by x desc', PsCheck::positiveInt($y));
    }

    /**
     * Метод загружает ячейки группы от левого края к правому
     * 
     * @param int $y - номер группы
     */
    public function getGroupCells($y) {
        return $this->getArray('select id_cell, x, y, v_text, dt_event, b_ban from crop_cell where y=? order by x desc', PsCheck::positiveInt($y));
    }

    /**
     * Метод загружает ячейки группы от левого края к правому
     * 
     * @param int $y - номер группы
     */
    public function getGroupCellsShort($y) {
        return $this->getArray('select id_cell, b_ban from crop_cell where y=? order by x desc', PsCheck::positiveInt($y));
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
     * @return CropCell ячейка
     */
    public function getCell($cellId, $required = true) {
        return PsCheck::isInt($cellId) ? $this->getObject('select * from crop_cell where id_cell=?', array($cellId), CropCell::getClass(), null, $required) : ($required ? raise_error('Не передан код ячейки') : null);
    }

    /**
     * Метод загружает эмоции
     * 
     * @return array массив эмоций
     */
    public function getEmotions() {
        $statistic = $this->getMap('select n_em as id, count(1) as value from crop_cell group by n_em');
        $emotions = array();
        foreach (CropConst::getEmotionsCodes() as $code) {
            $emotions[$code] = new EmotionStatistic($code, array_get_value($code, $statistic, 0));
        }
        return $emotions; //---
    }

    /** @return CropBean */
    public static function inst() {
        return parent::inst();
    }

}
