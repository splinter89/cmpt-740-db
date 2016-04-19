<?php

require 'classes/Autoloader.php';
Autoloader::addNamespace('', dirname(__DIR__).DIRECTORY_SEPARATOR.'classes');
require_once 'functions.php';
require_once 'queries.php';

error_reporting(E_ALL & ~E_NOTICE);
ini_set('error_log', dirname(__DIR__).DIRECTORY_SEPARATOR.'error.log');
Log::setUp(ini_get('error_log'));
DB::setUp(require 'db_config.php', 'test_server');

define('READ_DB_CONNECTION_NAME', chooseConnectionName(true));
define('WRITE_DB_CONNECTION_NAME', chooseConnectionName(false));
