<?php

declare(strict_types=1);

use JetBrains\PhpStorm\NoReturn;
use Booking\Config;

if (!function_exists('config')) {
    /**
     * Get the config value based on name.
     *
     * @param string $name
     * @param null $default
     * @return mixed
     */
    function config(string $name, $default = null): mixed
    {
        return Config::get($name, $default);
    }
}

if (!function_exists('is_production')) {
    /**
     * Get the config value based on name.
     *
     * @return boolean
     */
    function is_production(): bool
    {
        return (bool) config('app.env') == 'production';
    }
}


if (!function_exists('dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param mixed $args
     * @return void
     */
    #[NoReturn] function dd(...$args): void
    {
        http_response_code(500);

        foreach ($args as $x) {
            dump($x);
        }

        die(1);
    }
}

if (!function_exists('bootstrapError')) {
    /**
     * Returns a json in case bootstrap error.
     *
     * @param Throwable $t
     * @return void
     */
    #[NoReturn] function bootstrapError(Throwable $t): void
    {
        $data = [
            'type' => get_class($t),
            'message' => $t->getMessage(),
            'code' => 500,
            'trace' => $t->getTrace(),
        ];
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode($data);
        die(1);
    }
}

if (!function_exists('errorLog')) {
    /**
     * A simple logger to use in application bootstrap.
     *
     * @param Throwable $t
     * @return void
     */
    function errorLog(Throwable $t): void
    {
        $message = sprintf(
            "[%s] Exception: %s\nTrace:\n%s",
            date('Y-m-d H:i:s'),
            $t->getMessage(),
            $t->getTraceAsString()
        );

        $file = __DIR__ . '/../log/errors.log';
        file_put_contents($file, $message . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

if (!function_exists('infoLog')) {
    /**
     * A simple logger to use in application bootstrap.
     *
     * @param string $message
     * @return void
     */
    function infoLog(string $message): void
    {
        $message = sprintf(
            '[%s] Log: %s',
            date('Y-m-d H:i:s'),
            $message
        );

        $file = __DIR__ . '/../log/info.log';
        file_put_contents($file, $message . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
