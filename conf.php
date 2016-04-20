<?php
/**
 * Created by DashTec - Services
 *
 *      SnapCare 2015
 *
 * Date: 09.10.2015
 * Time: 22:34
 * v0.0.13.001
 * CUT: 2015-11-13 21:29:18
 */


$app = new Slim\App([
    'settings' => [
        'determineRouteBeforeAppMiddleware' => true,
        'app.version' => '0.0.1',
        'db.name' => 'xxxxxxxxx',
        'db.user' => 'xxxxxxxxx',
        'db.host' => 'localhost',
        'db.password' => 'xxxxxx',
        'displayErrorDetails' => true,
        'renderer' => [
            'template_path' => __DIR__ . '/views/',
        ],
        'middleware' => [
            'authentication' => [
                'filter_mode' => \DashTec\Middleware\AbstractFilterableMiddleware::EXCLUSION,
                'route_names' => ['login'],
            ],
            'authorization' => [
                'filter_mode' => \DashTec\Middleware\AbstractFilterableMiddleware::INCLUSION,
                'route_names' => ['root', 'basic', 'inventory', 'op','disorder','medkomp','request','reservation','beta'],
                'route_group_mappings' => [
                    'root'          => ['root_permission'],
                    'basic'         => [
                        'root_permission',
                        'inventory_permission',
                        'operator_permission',
                        'disorder_permission',
                        'medkomp_permission',
                        'request_permission',
                        'reservation_permission',
                        'beta_permission'],
                    'inventory'     => ['root_permission', 'inventory_permission'],
                    'op'            => ['root_permission', 'operator_permission'],
                    'disorder'      => ['root_permission', 'disorder_permission'],
                    'medkomp'       => ['root_permission', 'medkomp_permission'],
                    'request'       => ['root_permission', 'request_permission'],
                    'reservation'   => ['root_permission', 'reservation_permission'],
                    'beta'          => ['root_permission', 'beta_permission'],
                ],
            ],
        ],
    ],
]);