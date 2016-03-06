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
    public static function resetGroup($y) {
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
     * Метод выполняет бан ячейки
     * 
     * @param int $cellId - код ячейки
     */
    public static function banCell($cellId) {
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
    public static function unbanCell($cellId) {
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

}

?>
