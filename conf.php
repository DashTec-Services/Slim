<?php
$app = new \Slim\Slim([
    'mylov.version' => '0.1',
    'mylov.version_clear' => '01',
    'db.user' => 'wrsSQLuser',
    'db.password' => '',
    'db.name' => '',
    'demo_mod' => false,
    'view' => new \SP\Views\MyUltimateView(),
    'templates.path' => __DIR__ . '/views',
    'db.host' => 'localhost',
    'db.port' => 3306,
    'db.encoding' => 'utf8',
    'php.timezone' => 'Europe/Berlin',
    'php.error-reporting' => E_ALL | E_STRICT,
    'middleware.authentication' => [
        'filter_mode' => \SP\Middleware\AbstractFilterableMiddleware::EXCLUSION,
        'route_names' => ['login', 'doLogin', 'doLogout', 'logout'],
    ],
    'middleware.authorization' => [
        'filter_mode' => \SP\Middleware\AbstractFilterableMiddleware::INCLUSION,
        'route_names' => ['restricted', 'consumer'],
        'route_group_mappings' => [
            'restricted' => ['adm'],
            'consumer' => ['usr'],
            'both' => ['adm', 'usr'],
        ],
    ],
]);


