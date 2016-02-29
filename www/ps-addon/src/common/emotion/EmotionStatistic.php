<?php

/**
 * Статистика об эмоции
 *
 * @author azaz
 */
class EmotionStatistic {

    /**
     * @var int - код эмоции
     */
    private $code;

    /**
     * @var int - кол-во голосов за эту эмоцию
     */
    private $cnt;

    /**
     * Статистика по эмоции
     * 
     * @param int $code - код эмоции
     * @param int $cnt - кол-во голосов за эту эмоцию
     */
    public function __construct($code, $cnt) {
        $this->code = $code;
        $this->cnt = PsCheck::intOrNull($cnt);
    }

    function getCode() {
        return $this->code;
    }

    function getCnt() {
        return $this->cnt;
    }

    function getName() {
        return CropConst::getEmotionName($this->code);
    }

    function getDescr() {
        return CropConst::getEmotionDescr($this->code);
    }

    public function __toString() {
        return 'EmotionStatistic [code=' . $this->code . ', cnt=' . $this->cnt . ', name=' . $this->getName() . ', descr=' . $this->getDescr() . ']';
    }

}
