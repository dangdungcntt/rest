<?php

require_once __DIR__.'/vendor/autoload.php';

define('__ROOT__', __DIR__);

if (file_exists(__DIR__.'/.env')) {
    Dotenv\Dotenv::createImmutable(__DIR__)->load();
}