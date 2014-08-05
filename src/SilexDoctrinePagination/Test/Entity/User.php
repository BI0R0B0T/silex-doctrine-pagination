<?php
/**
 * @authot Dolgov_M <mdol@1c.ru>
 * @date 05.08.14 18:22
 */

namespace SilexDoctrinePagination\Test\Entity;


class User {
    /**
     * @return string
     */
    public static function getClassName(){
        return get_called_class();
    }
} 