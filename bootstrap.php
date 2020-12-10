<?php

//From v1.1.0 using symfony/var-dumper
$GLOBALS['__composer_autoload_files'][md5('symfony/var-dumper:Resources/functions/dump.php')] = true;

require_once __DIR__.'/vendor/autoload.php';

define('__ROOT__', __DIR__);

if (file_exists(__DIR__.'/.env')) {
    Dotenv\Dotenv::createImmutable(__DIR__)->load();
}