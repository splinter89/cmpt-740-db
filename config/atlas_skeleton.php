<?php

$container = require 'container.php';

return [
    'pdo' => [
        $container->get('db.dsn.default'),
        $container->get('db.username'),
        $container->get('db.password'),
    ],
    'namespace' => 'App\\DataSource',
    'directory' => './src/DataSource',
];
