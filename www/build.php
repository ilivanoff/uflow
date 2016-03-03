<?php

/*
 * Скрипт позволяет перестроить группы
 */
header('Content-Type: text/html; charset=utf-8');

require_once 'ps-includes/MainImport.php';

ExceptionHandler::registerPretty();

CropGroupImgGenerator::makeGroups(RequestArrayAdapter::inst()->bool('force'));
?>