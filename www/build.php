<?php

/*
 * Скрипт позволяет перестроить группы
 */
header('Content-Type: text/html; charset=utf-8');

require_once 'ps-includes/MainImport.php';

CropGroupsGenerator::makeGroups(RequestArrayAdapter::inst()->bool('force'));
?>