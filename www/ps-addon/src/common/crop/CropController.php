<?php

/**
 * Класс содержит различные управляющие методы для работы с ячейками
 *
 * @author azazello
 */
class CropController {

    /**
     * Метод сбрасывает группу изображений:
     * 1. Удаляет изображение группы
     * 2. Чистит кеш карты по группе
     * 
     * @param int $y - код группы
     */
    public static final function resetGroup($y) {
        //Проверим код группы
        $y = PsCheck::positiveInt($y);
        //Чистим кеш по группе
        CropCache::GROUPS()->removeFromCache("$y");
        //Удаляем узображение группы
        DirManagerCrop::groupFile($y)->remove();
        //Создаём группу изображений
        CropGroupImgGenerator::makeGroup($y);
    }

    /**
     * Метод сбрасывает все группы изображений
     * 1. Удаляет изображение группы
     * 2. Чистит кеш карты по группе
     */
    public static final function resetAllGroups() {
        $maxY = CropBean::inst()->getMaxY();
        if (PsCheck::isInt($maxY)) {
            for ($y = $maxY; $y >= 1; --$y) {
                self::resetGroup($y);
            }
        }
    }

    /**
     * Метод выполняет бан ячейки
     * 
     * @param int $cellId - код ячейки
     */
    public static final function banCell($cellId) {
        //Инициализируем переменную
        $banned = false;

        //Загрузим ячейку
        $cell = CropBean::inst()->getCell($cellId);
        //Уже забанена? Выходим!
        if ($cell->isBanned()) {
            return $banned; //---
        }

        //Баним!
        $banned = CropBean::inst()->banCell($cellId);

        //Фактически забанили? Выполним ряд действий
        if ($banned) {
            //Очистим группу, в которую входит ячейка
            self::resetGroup($cell->getY());
            //Аудит
            CropAudit::cellBanned($cell);
        }

        //Возвращаем признак фактического выполнения
        return $banned;
    }

    /**
     * Метод выполняет разбан ячейки
     * 
     * @param int $cellId - код ячейки
     */
    public static final function unbanCell($cellId) {
        //Инициализируем переменную
        $unbanned = false;

        //Загрузим ячейку
        $cell = CropBean::inst()->getCell($cellId);
        //Не забанена? Выходим!
        if (!$cell->isBanned()) {
            return $unbanned; //---
        }

        //Разбаним!
        $unbanned = CropBean::inst()->unbanCell($cellId);

        //Фактически забанили? Выполним ряд действий
        if ($unbanned) {
            //Очистим группу, в которую входит ячейка
            self::resetGroup($cell->getY());
            //Аудит
            CropAudit::cellUnbanned($cell);
        }

        //Возвращаем признак фактического выполнения
        return $unbanned;
    }

    /**
     * Метод снимает дамп группы
     */
    public static function makeGroupDump($y) {
        $cells = CropBean::inst()->getGroupCellsObj($y);
        //Нет ячеек? Выходим!
        if (empty($cells)) {
            return null; //---
        }

        //Подготовим zip
        $zip = DirManager::autoNoDel('crop-dumps')->getDirItem(null, "$y", PsConst::EXT_ZIP)->getZipWriteFileAdapter();

        //В комментарий добавим метку времени
        $zip->setComment('Dumped at: ' . time());

        //Добавим изображения
        /* @var $cell CropCell */
        foreach ($cells as $cell) {
            $cellId = $cell->getCellId();
            foreach (array(CropConst::TMP_FILE_BIG, CropConst::TMP_FILE_SMALL) as $type) {
                $di = DirManagerCrop::cropDi($cellId, $type);
                if ($di->isImg()) {
                    $zip->addItem($di);
                }
            }
        }

        //Добавим дамп аудита по этой группе
        $zip->addTableDump(Query::select('*', 'ps_audit', array('id_process' => CropConst::AUDIT_PROCESS_CODE, 'id_type' => $y), null, 'dt_event asc, id_rec asc'));

        //Добавим дамп ячеек по этой группе
        $zip->addTableDump(Query::select('*', 'crop_cell', array('y' => $y), null, 'n asc'));

        //Зыкрываем массив и возвращаем его
        return $zip->close();
    }

    /**
     * Метод снимает полный дамп БД
     */
    public static final function makeTotalDbDump() {
        //Подготовим zip
        $zip = DirManager::autoNoDel('crop-dumps')->getDirItem(null, 'db', PsConst::EXT_ZIP)->getZipWriteFileAdapter();

        //В комментарий добавим метку времени
        $zip->setComment('Dumped at: ' . time());

        //Добавим дамп полного аудита
        $zip->addTableDump(Query::select('*', 'ps_audit', array('id_process' => CropConst::AUDIT_PROCESS_CODE), null, 'dt_event asc, id_rec asc'));

        //Добавим дамп всех ячеек
        $zip->addTableDump(Query::select('*', 'crop_cell', null, null, 'n asc'));

        //Зыкрываем массив и возвращаем его
        return $zip->close();
    }

    /**
     * Метод снимает полный дамп БД
     */
    public static final function sendTotalDbDump($email) {
        //Проверим почту
        $email = PsCheck::email($email);
        //Дамп ячеек
        $di = self::makeTotalDbDump();
        //Отправляем mail
        $proj = ConfigIni::projectName();
        $time = time();
        $date = DatesTools::inst()->uts2dateInCurTZ($time, DF_PS);
        //Формируем и отправляем письмо
        $mail = PsMailSender::inst();
        $mail->SetSubject("Dump of $proj on $date");
        $mail->SetBody("Dump of $proj on $date ($time)");
        $mail->AddAttachment($di->getAbsPath(), $di->getName());
        $mail->AddAddress($email);
        $mail->Send();
    }

}

?>