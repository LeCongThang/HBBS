<?php
function assets($url)
{
    return BASE_URL . $url;
}

function lang($line = '')
{
    return \App\Lib\Web\Language::getInstant()->line($line);
}

function cl()
{
    $prefix = \App\Lib\Web\Language::getCurrentPrefix();
    if ($prefix != '') {
        $prefix = '_'.$prefix;
    }
    return $prefix;
}

function getUrlLanguage()
{
    $prefix = \App\Lib\Web\Language::getCurrentPrefix();
    if ($prefix != '') {
        $prefix = $prefix.'/';
    }
    return $prefix;
}

function url($url)
{
    return BASE_URL . getUrlLanguage() . ltrim($url, '/');
}