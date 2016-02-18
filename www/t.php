<?php

header('Content-Type: text/html; charset=utf-8');

require_once 'ps-includes/MainImport.php';

ExceptionHandler::registerPretty();

CropUploader::upload(null, null, null, array());

die;

echo DirManagerCrop::cropAuto(3)->absDirPath();
echo DirManagerCrop::cropExists(3);

//CropBean::inst()->saveCell();

die;

//PsMailSender::fastSend('Hello', 'My body', 'azazello85@mail.ru');

$sender = PsMailSender::inst();
$sender->SetSubject('Прикреплённые файлы');
$sender->SetBody('Файлы к записи на публикаторе');
$sender->AddAddress('azazello85@mail.ru', 'Илья');
$sender->AddAttachment(DirItem::inst('ps-addon/crop/imgo.jpeg')->getAbsPath(), 'imgo.jpeg');
$sender->Send();
?>