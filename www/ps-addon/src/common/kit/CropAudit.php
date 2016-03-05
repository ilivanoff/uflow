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

    /**
     * Аудит регистрации пользователя
     */
    public static function cellAdded(CropCell $cell) {
        parent::newRec(self::ACTION_ADDED)->setInstId($cell->getCellId())->submit();
    }

}

?>