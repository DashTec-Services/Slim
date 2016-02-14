<?php
$lifetime=90000;
session_start();
setcookie(session_name(),session_id(),time()+$lifetime);
# MicroTime for Devel.
$beginn = microtime(true);

require_once __DIR__ . '/core/SplClassLoader.php';
require_once __DIR__ . '/core/DB.php';
include_once __DIR__ . '/core/request/Requests.php';

(new SplClassLoader('Slim', __DIR__ . '/core'))->register();
(new SplClassLoader('SP', __DIR__ . '/core'))->register();
(new SplClassLoader('core', __DIR__))->register();

#require_once __DIR__ . '/conf.php';

$app->add(new \SP\Middleware\AuthorizationMiddleware());
$app->add(new \SP\Middleware\AuthenticationMiddleware());



require_once __DIR__ . '/GlobeVar.php';

# Basic - Routes
#require_once __DIR__ . '/config/database.php';
#require_once __DIR__ . '/routes/authentication.php';


# Routes by User
require_once __DIR__ . '/auto_route_loader.php';

# DOMAIN - ERKENNUNG
$host = explode('.', $_SERVER['HTTP_HOST']);
$subdomain = $host[0];


# GETTEXT Cofig
if (empty($_SESSION['local'])) {
#   $local = DB::queryFirstRow("SELECT default_local FROM config WHERE id=%s", '1');
  #  $_SESSION['local'] = $local['default_local'];
}
$local = $_SESSION['local'];  # de_DE , en_US, es_MX
putenv("LC_ALL=" . $local . ".utf8");
setlocale(LC_ALL, $local . ".utf8");
bindtextdomain($local, __DIR__ . '/locale');
textdomain($local);

if (!defined('SP_AREA')) {
  #  $app->run();
}


if (false) {
    echo "<pre>";
    echo "GET:";
    print_r($_GET);

    echo "POST:";
    print_r($_POST);

    echo "SESSION:";
    print_r($_SESSION);

    echo "</pre>";
}
if(false){
    $endTime = microtime(true);
    $elapsed = $endTime - $beginn;
    echo "Execution time : $elapsed seconds";
}