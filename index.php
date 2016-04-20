<?php
require_once __DIR__ . '/vendor/autoload.php';
$lifetime = 900000;
session_start();
setcookie(session_name(), session_id(), time() + $lifetime);
# MicroTime for Devel.
$beginn = microtime(true);

/**
 *      Config load
 */

require_once 'conf.php';

/**
 *      Render-Engine
 */
$container = $app->getContainer();
// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

/**
 *      Include der Middleware
 */
$app->add(new \DashTec\Middleware\AuthenticationMiddleware($app));


/**
 *      Laden der Routen
 */
require_once 'routes.php';


/**
 *      Class Namespace - Autoloader
 */
include_once 'library/NamespaceAutoloader.class.php';
spl_autoload_register(array('NamespaceAutoloader', 'autoload'));

//Assets Dir
const INSTALL_DOMAIN = "https://app.wr-schule.de";

// Settings Access
$_SESSION['SlimSettings'] = $container['settings'];

DB::$user = $_SESSION['SlimSettings']['db.user'];
DB::$password = $_SESSION['SlimSettings']['db.password'];
DB::$dbName = $_SESSION['SlimSettings']['db.name'];
DB::$host = $_SESSION['SlimSettings']['db.host'];



$app->run();
