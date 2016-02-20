<?php

header('Content-Type: text/html; charset=utf-8');

require_once 'ps-includes/MainImport.php';

ExceptionHandler::registerPretty();

PsUtil::startUnlimitedMode();

//CropTests::clean();
CropTests::makeCropCells(1000);
//CropGroupsGenerator::makeGroup(1);
//echo PsHtml::img(array('src' => DirItem::inst('g', 1, CropConst::CROP_EXT)));

die;

$cellsCnt = 17;
$cellsCnt = $cellsCnt - 1;
$x = 1 + $cellsCnt % CropConst::CROPS_GROUP_CELLS;
$y = 1 + round(($cellsCnt - $x) / CropConst::CROPS_GROUP_CELLS);

echo "$y x $x";


die;


$idCell = CropBean::inst()->makeCell('xxx', 'my text');
echo CropBean::inst()->submitCell($idCell);

//echo basename(DirManagerCrop::cropTempDir()->relDirPath());

die;

//PsMailSender::fastSend('Hello', 'My body', 'azazello85@mail.ru');

$sender = PsMailSender::inst();
$sender->SetSubject('Прикреплённые файлы');
$sender->SetBody('Файлы к записи на публикаторе');
$sender->AddAddress('azazello85@mail.ru', 'Илья');
$sender->AddAttachment(DirItem::inst('ps-addon/crop/imgo.jpeg')->getAbsPath(), 'imgo.jpeg');
$sender->Send();
?>