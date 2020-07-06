<?php

use App\Application;
use App\Support\DumpDieException;
use App\Support\Response;
use App\Support\ViewResponse;

if (!function_exists('app')) {
    function app()
    {
        return Application::getInstance();
    }
}

if (!function_exists('view')) {
    function view(string $viewName, array $data = [])
    {
        return new ViewResponse($viewName, $data);
    }
}

if (!function_exists('response')) {
    function response()
    {
        return new Response();
    }
}

if (!function_exists('env')) {
    function env(string $key, $default = null): string
    {
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('dd')) {
    /**
     * @param  mixed  ...$vars
     * @throws DumpDieException
     */
    function dd(...$vars)
    {
        ob_start();
        var_dump(...$vars);
        throw new DumpDieException(ob_get_clean());
    }
}

function deleteDir(string $dir, $preserve = false)
{
    foreach (glob("$dir/*") as $item) {
        is_dir($item) ? deleteDir($item) : unlink($item);
    }

    if (!$preserve) {
        @rmdir($dir);
    }
}