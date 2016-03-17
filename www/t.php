<?php

header('Content-Type: text/html; charset=utf-8');

require_once 'ps-includes/MainImportAdmin.php';

PsDefines::assertProductionOff(__FILE__);

ExceptionHandler::registerPretty();

PsUtil::startUnlimitedMode();

$keys = array_keys(array(1, 2, 3, 4));
$keys2 = $keys;
shuffle($keys2);

print_r($keys);
br();
print_r($keys2);

die;

for ($i = 0; $i < 10; $i++) {
    $citata = PsCitates::citata();
    echo $citata[0] . ': ' . $citata[1];
    br();
}

die;

//ActivityWatcher::registerActivity();
$author = '';
$authorC = '';
$text = '';
$textC = '';
foreach (PsCitates::citates() as $c) {
    $tokens = explode(' | ', $c, 2);
    $a = $tokens[0];
    if (ps_strlen($a) > ps_strlen($author)) {
        $author = $a;
        $authorC = $c;
    }
    $t = $tokens[1];
    if (ps_strlen($t) > ps_strlen($text)) {
        $text = $t;
        $textC = $c;
    }
}

echo $author;
br();
echo $authorC;
br();
echo $text;
br();
echo $textC;

die;

//echo DatesTools::inst()->uts2dateInCurTZ(time(), DF_PS_HM);

$command = '"C:\Program Files (x86)\Java\jdk1.7.0_79\bin\java" -jar "C:\Users\azaz\Downloads\compiler-latest\yuicompressor-2.4.8.jar" --type js  --charset UTF-8 -o "C:\Users\azaz\Downloads\compiler-latest\core.yu.min.js" "C:\Users\azaz\Downloads\compiler-latest\core.js"';
shell_exec($command);

$command = '"C:\Program Files (x86)\Java\jdk1.7.0_79\bin\java" -jar "C:\Users\azaz\Downloads\compiler-latest\compiler.jar" --js "C:\Users\azaz\Downloads\compiler-latest\core.js" --js_output_file "C:\Users\azaz\Downloads\compiler-latest\core.cl.min.js"';
//shell_exec($command);

$command = '"C:\Program Files (x86)\Java\jdk1.7.0_79\bin\java" -jar "C:\Users\azaz\Downloads\compiler-latest\compiler.jar" --css "C:\Users\azaz\Downloads\compiler-latest\client.css" --css_output_file "C:\Users\azaz\Downloads\compiler-latest\client.cl.min.css"';
//shell_exec($command);
//CropController::sendTotalDbDump('azazello85@mail.ru');
//CropController::makeGroupDump(706);
//CropController::makeTotalDbDump();

die;

$zip = DirManager::inst()->getDirItem(null, 'xxx', 'zip')->getZipWriteFileAdapter();
$zip->addItem(DirItem::inst('/c/9243/big.png'));
$zip->addItem(DirItem::inst('/c/9244'));
$zip->addFromString('ps_audit.sql', PsTable::inst('ps_audit')->exportAsSqlString());
$zip->close();

die;


//Query::select($what, $table, $where, $group, $order, $limit);
//print_r(PsTable::inst('ps_audit')->exportAsSqlString(PS_ACTION_CREATE, Query::select('*', 'ps_audit', array('id_process' => 100), null, 'dt_event asc, id_rec asc', null)));
print_r();

die;

echo CropController::banCell(11256);
//echo CropController::unbanCell(11256);
//echo CropController::banCell(749);
//CropController::resetGroup(704);

print_r(PsReflect::describeMethod('CropController', 'resetGroup'));

die;

$r = new ReflectionMethod('CropController', 'resetGroup');
$doc = $r->getDocComment();
preg_match_all('#@(.*?)\n#s', $doc, $annotations);
print_r($annotations[1]);

die;

for ($i = 0; $i < 10; $i++) {
    echo PsHtml::img(array('src' => DirManagerCrop::banDiSmall($i)));
}


// echo CropController::banCell(11269);
//echo CropController::unbanCell(11269);
//CropController::resetGroup(47);

die;


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