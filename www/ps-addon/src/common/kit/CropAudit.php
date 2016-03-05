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
    public static function cellAdded($cellId) {
        parent::doAudit(self::ACTION_ADDED, null, null, $cellId);
    }

}

?>