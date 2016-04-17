<?php

require 'classes/Autoloader.php';
Autoloader::addNamespace('', dirname(__DIR__).DIRECTORY_SEPARATOR.'classes');
require_once 'functions.php';

Log::setUp(ini_get('error_log'));
DB::setUp(require 'db_config.php', 'test_server');
