<?php


$app = new Slim\App([
    'php.error-reporting' => E_ALL | E_STRICT,
    'middleware.authentication' => [
        'filter_mode' => \SP\Middleware\AbstractFilterableMiddleware::EXCLUSION,
        'route_names' => ['login', 'doLogin', 'doLogout', 'logout'],
    ],
    'middleware.authorization' => [
        'filter_mode' => \SP\Middleware\AbstractFilterableMiddleware::INCLUSION,
        'route_names' => ['adm_admin', 'admin', 'reseller', 'user','ekuser','restricted','sms'],
        'route_group_mappings' => [
            'adm_admin' => ['adm_admin'],
            'admin' => ['admin'],
            'reseller' => ['reseller'],
            'user' => ['user'],
            'ekuser' => ['ekuser'],
            'restricted' => ['adm_admin','admin', 'reseller' ,'user', 'ekuser'],
        ],
    ],
]);
