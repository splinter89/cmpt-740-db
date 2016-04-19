<?php

return [
    'default' => 'master',
    'connections' => [
        'master' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => '740_project',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        'slave1' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3307',
            'database' => '740_project',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        'slave4_vm' => [
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
