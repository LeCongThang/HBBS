<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 12/21/2016
 * Time: 10:05 AM
 */

namespace app\lib\base;


class Component
{
    protected static $instant;


    /**
     * @return mixed
     */
    public static function getInstant()
    {
        if (static::$instant == null) {
            $className = get_called_class();
            static::$instant = new $className();
        }
        return static::$instant;
    }


}