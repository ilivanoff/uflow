<?php

header('Content-Type: text/html; charset=utf-8');

require_once 'ps-includes/MainImport.php';

PsDefines::assertProductionOff(__FILE__);

ExceptionHandler::registerPretty();

PsUtil::startUnlimitedMode();


$str = "  a    \n     b\n     c   ";
$str = normalize_string($str);
echo $str;

die;

CropAudit::cellAdded(1);

die;

for ($i = 0; $i < 10; $i++) {
    PsIp::ban(PsRand::ip());
}

PsIp::unbanAll();

PsIp::ban(PsRand::ip());

PsIp::unban('197.111.221.29');

die;
echo PsCheck::ip('1.2.3.4');

die;

//require_once 'ps-addon/lib/recaptcha-master/src/ReCaptcha/ReCaptcha.php';
require_once 'ps-addon/lib/recaptcha-master/src/autoload.php';

$gRec = '03AHJ_VuuxVrlfOD_EOiXL8whsjw5tadqy6mFFIgTALFLjGe0wZRcLXnzK8VLkxgpfCNas6QDRP6kAgv0ARTBPQIwJeHq9KtmYCg_H0Xf5ks9QJTEi95Y0BhWDWyCQu78qUK3td0T6N0XboqwE0Shdyf1Kp8rlh3Aw8lrvY9vLIF-_Wj2O7aZWpiDpVsC14dv1px55MXfbfrngvysswrYpUCrI7opcPVRwYEqeuzqPefK_HDJ_gEsP1F2Pn0KDTiZAt7NRn50BuejsPqUw7lZWtZFAaqxBCWqh1cRZywz-poNaklxWCY2fOSUtlEjJL5nvY9F3VCPRe0COselnVWA5tE0gd6KzuE43kOKNCk4A4ILY9SFj9ikmf2QdqCeqIYKRDJpkIxxCrTftwJPA446nSSFUhnfvw7ncsNV0ZpiKNSDB-EMaLCEp_7nK13DUcBGZvu_gaxCVMtcErSFQRa6yqexIE4Vl8LtTZFTMWM9epE2GykrgJFc0VrAHu0Yi-7IepmIR9kbbo-Pr6VrVt3CNlBbTtr1Fr-Bc6AKgBwxE6kJQ70GX8NsCprcBYG2u0vSIRUQKVYUTXvu1Tqmq2wSGfZgmKMKZx2Mck7c4aw3Bje7lq6l5NWqW18qvJbTmrcOXUv6UtPDXLx9-noTY9wJfuwFvgfO3tNdbwzO1ZOwgXgs56EUTX9e6ov_CakFQanyoQjcyNBZ4O3S61RtWNtVqbGaP_EvCoUQXsBgoP65ozSgdNTPjf-qJvxFfW1y2ALmN1ekWvNsbv7YIZX31Lq2qO3CFFXr4wFnxdSR53nrX1LkuI88WEi4ootVAnU45CxBQT4WkC1204abP9azyA0FWB2A90FxuyOWZEMpNocRsKQsWYTAze0SdO7p3CQuOLLFphacDFtnUskWybnTpmRHg5zN7oIzKodN009NtYZQLt8Gw_jzMmOIa9ynRsn2WDcEmo8lPGTD7QVClc--aLN91dSwVbYLiE-AgacWsvCyXWZNumViPvVR1NB0';
//echo PSreCAPTCHA::isValid($gRec);

$recaptcha = new \ReCaptcha\ReCaptcha(CROP_CAPTCHA_PRIVATE);
$resp = $recaptcha->verify($gRec, $_SERVER['REMOTE_ADDR']);
if ($resp->isSuccess()) {
    echo 'verified!';
} else {
    $errors = $resp->getErrorCodes();
    print_r($errors);
}


//CropTests::makeCropCells(50);

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