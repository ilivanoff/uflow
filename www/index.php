<?php

require_once 'ps-includes/MainImport.php';

header('Content-Type: text/html; charset=utf-8');
ExceptionHandler::registerPretty();

$suffix = RequestArrayAdapter::inst()->str('page', 'wall');
$tplPath = "crop/$suffix.tpl";
if (!PSSmarty::smarty()->templateExists($tplPath)) {
    $suffix = 'wall';
    $tplPath = "crop/$suffix.tpl";
}

$SMARTY_PARAMS['JS_DEFS'] = PageBuilder::inst()->buildJsDefs();
$SMARTY_PARAMS['CROP_SUFFIX'] = $suffix;
$PARAMS['RESOURCES'] = PSSmarty::template('crop/page_resources.tpl', $SMARTY_PARAMS)->fetch();
$PARAMS['CONTENT'] = PSSmarty::template($tplPath)->fetch();
$PARAMS['TITLE'] = 'Мои мысли';
PSSmarty::template('page/page_pattern.tpl', $PARAMS)->display();
?>