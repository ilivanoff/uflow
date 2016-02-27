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
        $builderCtxt->setSmartyParams4Resources($this->basicPage->getSmartyParams4Resources());

        //4. GET SMARTY PARAMS FOR TPL
        $smartyParams['content'] = BasicPagesManager::inst()->getResourcesLinks($this->basicPage->getIdent(), ContentHelper::getContent($this->basicPage));
        return $smartyParams;
    }

    public function getProfiler() {
        return PsProfiler::inst('CropPageBuilder');
    }

}

?>