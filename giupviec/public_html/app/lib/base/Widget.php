<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 12/23/2016
 * Time: 9:14 AM
 */

namespace app\lib\base;


class Widget
{

    /**
     * @param $data
     * @return mixed
     */
    public static function run($data)
    {
        $class = get_called_class();
        $obj = new $class($data);

        foreach ($data as $key => $val) {
            if (property_exists($obj, $key)) {
                $obj->{$key} = $val;
            }
        }

        return $obj;
    }
}