<?php

declare(strict_types=1);

if (!function_exists('dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param mixed $args
     * @return void
     */
    function dd(...$args)
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
    function bootstrapError(Throwable $t)
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
    function errorLog(Throwable $t)
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
    function infoLog(string $message)
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
