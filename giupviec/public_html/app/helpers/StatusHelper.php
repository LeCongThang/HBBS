<?php
/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 12/21/2016
 * Time: 4:12 PM
 */

namespace app\helpers;


class StatusHelper
{
    public static function run($status)
    {
        if ($status === 1) {
            return '<label class="text-success"><i class="fa fa-check-circle"></i></label>';
        } else {
            return '<label class="text-danger"><i class="fa fa-circle-o"></i></label>';
        }
    }
}