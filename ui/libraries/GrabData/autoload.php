<?php
set_time_limit(0);
error_reporting(E_ERROR|E_NOTICE);

define("ROOT_PATH", __DIR__ . DIRECTORY_SEPARATOR);
define("IMAGES_PATH", ROOT_PATH . 'Images'.DIRECTORY_SEPARATOR.date("Ym").DIRECTORY_SEPARATOR);
define("CONFIG_PATH", ROOT_PATH . 'Conf'.DIRECTORY_SEPARATOR.'Config.php');
define("LOGS_PATH", ROOT_PATH . 'Logs' .DIRECTORY_SEPARATOR.date("Ym").DIRECTORY_SEPARATOR);
define("UTIL_PATH", ROOT_PATH . 'Util'.DIRECTORY_SEPARATOR);

if (!file_exists(LOGS_PATH)) {
    @mkdir(LOGS_PATH,777,true);
}

if (!file_exists(IMAGES_PATH)) {
    @mkdir(IMAGES_PATH,777,true);
}

function AutoLoader($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $classFile = ROOT_PATH .'Libraries'. DIRECTORY_SEPARATOR . $path . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
    }
    require_once CONFIG_PATH;

}
spl_autoload_register('AutoLoader');
?>