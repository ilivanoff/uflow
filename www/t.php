<?php

header('Content-Type: text/html; charset=utf-8');

require_once 'ps-includes/MainImport.php';

PsDefines::assertProductionOff(__FILE__);

ExceptionHandler::registerPretty();

PsUtil::startUnlimitedMode();

CropTests::makeCropCells(50);

die;

foreach (CropBean::inst()->getEmotions() as $emotion) {
    echo $emotion;
    br();
}

die;

foreach (PSDB::getArray('select id_cell as id from crop_cell') as $cell) {
    $id = $cell['id'];
    PSDB::update('update crop_cell set n_em=? where id_cell=?', array(CropTests::randomEmotionCode(), $id));
}
die;

CropTests::clean();
CropTests::makeCropCells(350);

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