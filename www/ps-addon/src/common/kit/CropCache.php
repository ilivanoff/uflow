<?php

/**
 * Description of CropCache
 *
 * @author azazello
 */
class CropCache {

    /**
     * Группа кеширования для оконченных групп
     * 
     * @return PSCacheGroup
     */
    public static function GROUPS() {
        return PSCacheGroup::inst(__CLASS__, __FUNCTION__);
    }

}

?>