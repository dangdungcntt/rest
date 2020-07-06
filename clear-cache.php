<?php

require_once __DIR__.'/bootstrap.php';

deleteDir(env('CACHE_PATH', __DIR__.'/cache').'/views', true);
echo "View cache cleared!".PHP_EOL;
echo "Done clear-cache command!".PHP_EOL;