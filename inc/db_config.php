<?php

return [
    'default' => 'master',
    'connections' => [
        'master1' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => '740_project',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        'slave' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3307',
            'database' => '740_project',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        'master2' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3308', // forwarded
            'database' => '740_project',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
    ],
    //'log' => 'C:/wamp/logs/php_error.log',
    'log' => 'C:/D/www/740_project/error.log',
];
