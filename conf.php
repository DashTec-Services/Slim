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

# Prüfung das install_DIR gelöscht wurde
if(is_dir("./install") ){
    // TODO Realease activate
    #die(_('Bitte den "install" Ordner löschen'));
}


$app = new \Slim\Slim([
    'sp.version' => '4.0.0',
    'sp.version_clear' => '400',
    'db.user' => 'spdevuser',              # @DBUSER@ sapdevuser
    'db.password' => '', 		    # @DBPASS@ jaro2812
    'db.name' => 'spdev',		            # @DBNAME@
    'demo_mod' => false,                     # @DEMOMOD@
    'view' => new \SP\Views\MyUltimateView(),
    'templates.path' => __DIR__ . '/views',
    'db.host' => 'localhost',
    'db.port' => 3306,
    'db.encoding' => 'utf8',
    'php.timezone' => 'Europe/Berlin',
    'php.error-reporting' => E_ALL | E_STRICT,
    'middleware.authentication' => [
        'filter_mode' => \SP\Middleware\AbstractFilterableMiddleware::EXCLUSION,
        'route_names' => ['login', 'doLogin', 'doLogout'],
    ],
    'middleware.authorization' => [
        'filter_mode' => \SP\Middleware\AbstractFilterableMiddleware::INCLUSION,
        'route_names' => ['restricted'], ['userrestricted'],
        'route_group_mappings' => [
            'restricted'    => ['adm'],
            'userrestricted'    => ['user' , 'dj'],
        ],
    ],
]);

