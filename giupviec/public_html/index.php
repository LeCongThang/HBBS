<?php

require 'vendor/autoload.php';
define('BASE_URL', 'http://proimage.vn/');
use app\lib\base\Router;

error_reporting(0);
ini_set('display_errors', 0);

date_default_timezone_set('Asia/Ho_Chi_Minh');

// defines the web root
define('WEB_ROOT', substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], '/index.php')));
// defindes the path to the files
define('ROOT_PATH', realpath(dirname(__FILE__) . '/') . '/');
// defines the cms path
define('CMS_PATH', ROOT_PATH . 'app/lib/base/');
define('LIB_PATH', ROOT_PATH . 'app/lib/');
// starts the session
session_start();

// includes the system routes. Define your own routes in this file
include(ROOT_PATH . 'app/config/routes.php');
include(ROOT_PATH . 'app/config/functions.php');
/**
 * Standard framework autoloader
 * @param string $className
 */
/*spl_autoload_register(function ($class) {
    $file = ROOT_PATH . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});*/

$router = new Router();
$router->execute($routes);
