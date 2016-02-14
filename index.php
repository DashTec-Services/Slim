<?php
/**
 * Created by David Schomburg (DashTec - Services)
 *      www.dashtec.de
 *
 *  S:P (StreamersPanel)
 *  Support: http://board.streamerspanel.de
 *
 *  v 4.0.0
 *
 *  Kundennummer:   @KDNUM@
 *  Lizenznummer:   @RECHNR@
 *  Lizenz: http://login.streamerspanel.de/user/terms
 */
session_start();

require_once __DIR__ . '/core/SplClassLoader.php';
require_once __DIR__ . '/core/DB.php';
include_once __DIR__ . '/core/request/Requests.php';

(new SplClassLoader('Slim', __DIR__ . '/core'))->register();
(new SplClassLoader('SP', __DIR__ . '/core'))->register();
(new SplClassLoader('core', __DIR__ ))->register();

require_once __DIR__ . '/conf.php';

$app->add(new \SP\Middleware\AuthorizationMiddleware());
$app->add(new \SP\Middleware\AuthenticationMiddleware());

# Active Routes
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/routes/authentication.php';

require_once __DIR__ . '/routes/basic.php';
require_once __DIR__ . '/routes/adm/admBasic.php';
require_once __DIR__ . '/routes/adm/admAddProject.php';
require_once __DIR__ . '/routes/adm/admChoice.php';
require_once __DIR__ . '/routes/adm/userList.php';
require_once __DIR__ . '/routes/adm/settings.php';
require_once __DIR__ . '/routes/adm/admexport.php';



# GETTEXT Cofig
if(empty($_SESSION['local'])){
    $_SESSION['local'] = 'de_DE';
}
$local = $_SESSION['local'];  # de_DE , en_US, es_MX
putenv("LC_ALL=".$local.".utf8");
setlocale(LC_ALL, $local.".utf8");
bindtextdomain($local, __DIR__.'/locale');
textdomain($local);



if(!defined('SP_AREA')){
$app->run();
}

