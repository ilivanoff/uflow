<?php

header('Content-Type: text/html; charset=utf-8');

require_once 'ps-includes/MainImport.php';

PsDefines::assertProductionOff(__FILE__);

ExceptionHandler::registerPretty();

PsUtil::startUnlimitedMode();


CropTests::makeCropCells(150);

//CropTests::clean();

//CropTests::clean();


die;

//CropTests::clean();
//CropTests::makeCropCells(2000);
//CropGroupsGenerator::makeGroups();
//print_r(ConfigIni::jsBrigeClasses());

foreach (ConfigIni::jsBrigeClasses() as $prefix => $class) {
    echo $prefix . ' : ' . $class;
    br();
    print_r($class);
}


die;


PsUtil::startUnlimitedMode();

print_r(CropCellsManager::inst()->loadCells4Show());

die;

$cellsCnt = 17;
$cellsCnt = $cellsCnt - 1;
$x = 1 + $cellsCnt % CropConst::CROPS_GROUP_CELLS;
$y = 1 + round(($cellsCnt - $x) / CropConst::CROPS_GROUP_CELLS);

echo "$y x $x";


die;

//PsMailSender::fastSend('Hello', 'My body', 'azazello85@mail.ru');

$sender = PsMailSender::inst();
$sender->SetSubject('Прикреплённые файлы');
$sender->SetBody('Файлы к записи на публикаторе');
$sender->AddAddress('azazello85@mail.ru', 'Илья');
$sender->AddAttachment(DirItem::inst('ps-addon/crop/imgo.jpeg')->getAbsPath(), 'imgo.jpeg');
$sender->Send();
?>