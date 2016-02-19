<?php

header('Content-Type: text/html; charset=utf-8');

require_once 'ps-includes/MainImport.php';

ExceptionHandler::registerPretty();

echo PsHtml::img(array('src' => CropTests::randomCropImgDi()));
echo PsHtml::img(array('src' => CropTests::randomCropImgBase64()));

echo CropTests::makeCropCells(5);

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