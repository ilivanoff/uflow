<?php

class BP_info extends BasicPage {

    public function getTitle() {
        return 'Информация';
    }

    public function doProcess(RequestArrayAdapter $params) {
        
    }

    public function buildContent() {
        echo $this->getFoldedEntity()->fetchTpl();
    }

    public function getJsParams() {
        
    }

    public function getSmartyParams4Resources() {
        
    }

}

?>