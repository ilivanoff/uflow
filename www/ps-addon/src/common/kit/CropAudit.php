<?php

/**
 * Аудит подсистемы для работы с ячейками
 *
 * @author azazello
 */
final class CropAudit extends PsAuditAbstract {
    /**
     * Действия
     */

    const ACTION_ADDED = 1;
    const ACTION_BANNED = 2;
    const ACTION_UNBANNED = 3;

    /**
     * Аудит создания ячейки
     */
    public static function cellAdded(CropCell $cell) {
        parent::newRec(self::ACTION_ADDED)->setInstId($cell->getCellId())->submit();
    }

    /**
     * Аудит бана ячейки
     */
    public static function cellBanned(CropCell $cell) {
        parent::newRec(self::ACTION_BANNED)->setInstId($cell->getCellId())->submit();
    }

    /**
     * Аудит разбана ячейки
     */
    public static function cellUnbanned(CropCell $cell) {
        parent::newRec(self::ACTION_UNBANNED)->setInstId($cell->getCellId())->submit();
    }

}

?>