<?php

require_once 'ps-includes/MainImport.php';

header('Content-Type: text/html; charset=utf-8');
ExceptionHandler::registerPretty();

$tplName = RequestArrayAdapter::inst()->str('page', 'wall');
$tplParams = array();

$cellId = RequestArrayAdapter::inst()->int('id');
if ($cellId) {
    $cell = CropBean::inst()->getCell($cellId, false);
    if ($cell) {
        $tplName = 'cell';
        $tplParams['cell'] = $cell;
    }
}

$tplPath = "crop/$tplName.tpl";
if (!PSSmarty::smarty()->templateExists($tplPath)) {
    $tplName = 'wall';
    $tplPath = "crop/$tplName.tpl";
}
PsDefines::setReplaceFormulesWithImages(false);

$SMARTY_PARAMS['JS_DEFS'] = PageBuilder::inst()->buildJsDefs();
$SMARTY_PARAMS['CROP_SUFFIX'] = $tplName;
$PARAMS['RESOURCES'] = PSSmarty::template('crop/page_resources.tpl', $SMARTY_PARAMS)->fetch();
$PARAMS['CONTENT'] = PSSmarty::template($tplPath, $tplParams)->fetch();
$PARAMS['TITLE'] = 'Мои мысли';
PSSmarty::template('page/page_pattern.tpl', $PARAMS)->display();
?>