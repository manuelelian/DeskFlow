<?php session_start();
error_reporting(-1);
// Definimos que todas las rutas van a ser relativas a la raiz del sitio
chdir(dirname(__DIR__));
define("CORE_PATH", "app/core/");
define("APP_PATH", "app/");
define("ROOT_PATH", "public/");

if (file_exists('app/librerias/vendor/autoload.php')) {
    require_once 'app/librerias/vendor/autoload.php';
    
    $dotenv = Dotenv\Dotenv::createImmutable('.');
    $dotenv->load();
}

require_once CORE_PATH."Autoloader.php";

$app = new App;

