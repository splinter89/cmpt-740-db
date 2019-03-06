<?php

declare(strict_types=1);

use App\App;

$base_dir = dirname(__DIR__);
require_once $base_dir.'/vendor/autoload.php';

$container = require $base_dir.'/config/container.php';
$router = require $base_dir.'/config/router.php';

$app = new App($container, $router);
$app->run();
