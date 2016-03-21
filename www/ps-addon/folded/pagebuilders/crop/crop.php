<?php

class PB_crop extends AbstractPageBuilder {

    /** @var BasicPage */
    private $basicPage;

    protected function doProcess(PageContext $ctxt, RequestArrayAdapter $requestParams, ArrayAdapter $buildParams) {
        $this->basicPage = BasicPagesManager::inst()->getPage($ctxt->getPage()->getPathBase());
        $this->basicPage->checkAccess();
        $this->basicPage->doProcess($requestParams);
    }

    protected function doBuild(PageContext $ctxt, PageBuilderContext $builderCtxt, RequestArrayAdapter $requestParams, ArrayAdapter $buildParams) {
        //1. ЗАГОЛОВОК
        $builderCtxt->setTitle($this->basicPage->getTitle());

        //2. JAVASCRIPT
        $builderCtxt->setJsParams($this->basicPage->getJsParams());


        //3. SMARTY RESOURCES
        $builderCtxt->setSmartyParam4Resources('PAGE', $this->basicPage->getIdent());
        $builderCtxt->setSmartyParams4Resources($this->basicPage->getSmartyParams4Resources());

        //4. GET SMARTY PARAMS FOR TPL
        $smartyParams['content'] = BasicPagesManager::inst()->getResourcesLinks($this->basicPage->getIdent(), ContentHelper::getContent($this->basicPage));

        //5. BUILD PARAMS
        $builderCtxt->setBuildOption(PageParams::BO_EXPORT_FOLDINDS, false); //Нам не нужны фолдинги

        return $smartyParams;
    }

    public function getProfiler() {
        return PsProfiler::inst('CropPageBuilder');
    }

    /**
     * Шаблон с ресурсами для страницы
     */
    public function getPageResourcesTpl() {
        return 'crop/page_resources.tpl';
    }

}

?>