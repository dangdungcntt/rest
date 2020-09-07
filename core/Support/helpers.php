<?php

use Core\Application;
use Core\Exceptions\DumpDieException;
use Core\Exceptions\HttpAbortException;
use Core\Support\Response;
use Core\Support\ViewResponse;

if (!function_exists('app')) {
    function app(?string $class = null, ?string $parentClass = null)
    {
        if ($class) {
            $parentClass ??= debug_backtrace(false)[1]['class'] ?? null;
            return Application::getInstance()->make($class, $parentClass);
        }

        return Application::getInstance();
    }
}

if (!function_exists('view')) {
    function view(string $viewName, array $data = []): ViewResponse
    {
        return (new ViewResponse())
            ->withView($viewName)
            ->withData($data);
    }
}

if (!function_exists('response')) {
    function response()
    {
        return new Response();
    }
}

if (!function_exists('env')) {
    function env(string $key, $default = null)
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


if (!function_exists('abort')) {
    /**
     * @param  int  $status
     * @param  mixed  $message
     * @throws HttpAbortException
     */
    function abort($status = 500, $message = 'Internal Server Error')
    {
        throw new HttpAbortException(is_array($message) ? json_encode($message) : $message, $status);
    }
}

if (!function_exists('abort_if')) {
    /**
     * @param $condition
     * @param  int  $status
     * @param  mixed  $message
     * @throws HttpAbortException
     */
    function abort_if($condition, $status = 500, $message = 'Internal Server Error')
    {
        if ($condition) {
            abort($status, $message);
        }
    }
}

if (!function_exists('abort_unless')) {
    /**
     * @param $condition
     * @param  int  $status
     * @param  mixed  $message
     * @throws HttpAbortException
     */
    function abort_unless($condition, $status = 500, $message = 'Internal Server Error')
    {
        abort_if(!$condition, $status, $message);
    }
}

if (!function_exists('logger')) {
    function logger($message)
    {
        if ($message instanceof Throwable) {
            $message = $message->getMessage().PHP_EOL.$message->getTraceAsString();
        }
        printf("[%s] {$message}".PHP_EOL, date('Y-m-d H:i:s'));
    }
}

if (!function_exists('deleteDir')) {
    function deleteDir(string $dir, $preserve = false)
    {
        foreach (glob("$dir/*") as $item) {
            is_dir($item) ? deleteDir($item) : unlink($item);
        }

        if (!$preserve) {
            @rmdir($dir);
        }
    }
}