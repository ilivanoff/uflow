<?php

class BP_cell extends BasicPage {

    /**
     * Ячейка
     * 
     * @var CropCell
     */
    private $cell;

    public function getTitle() {
        return 'Добавление ячейки';
    }

    public function doProcess(RequestArrayAdapter $params) {
        $this->cell = CropBean::inst()->getCell($params->int('id'), false);
        if (!$this->cell) {
            WebPages::redirectToIndex();
        }
    }

    public function buildContent() {
        echo $this->getFoldedEntity()->fetchTpl(array('cell' => $this->cell));
    }

    public function getJsParams() {
        return array('cell_id' => $this->cell->getCellId());
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>