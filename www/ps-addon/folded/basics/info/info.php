<?php

class BP_info extends BasicPage {

    private $emotions = null;

    public function getTitle() {
        return 'Информация';
    }

    public function doProcess(RequestArrayAdapter $params) {
        if (CROP_EM_STATISTIC_PIE) {
            $this->emotions = array();
            foreach (CropBean::inst()->getEmotions() as $em) {
                $this->emotions[] = array($em->getDescr(), $em->getCnt());
            }
        }
    }

    public function buildContent() {
        echo $this->getFoldedEntity()->fetchTpl();
    }

    public function getJsParams() {
        return array('em-chart-data' => $this->emotions);
    }

    public function getSmartyParams4Resources() {
        $params = array();
        if ($this->emotions) {
            $params['GOOGLE_CHARTS'] = true;
        }
        return $params;
    }

}

?>