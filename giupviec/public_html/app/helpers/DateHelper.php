<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 1/3/2017
 * Time: 1:47 PM
 */

namespace app\helpers;


class DateHelper
{
    public static function toSqlDate($strDate)
    {
        $date = \DateTime::createFromFormat('d/m/Y', $strDate);
        if ($date) {
            return $date->format('Y-m-d');
        }
        return null;
    }

    public static function formatDate($strDate)
    {
        $date = strtotime($strDate);
        return date('d/m/Y', $date);
    }
}