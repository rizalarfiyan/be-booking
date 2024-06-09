<?php

declare(strict_types=1);

use Booking\Config;
use Cake\Chronos\Chronos;
use Cake\Chronos\ChronosDate;
use Cake\Chronos\ChronosTime;
use JetBrains\PhpStorm\NoReturn;

if (! function_exists('config')) {
    /**
     * Get the config value based on name.
     *
     * @param string $name
     * @param null $default
     * @return mixed
     */
    function config(string $name, $default = null): mixed
    {
        return Config::getInstance()->get($name, $default);
    }
}

if (! function_exists('datetime')) {
    /**
     * Get the time with chronos library using paramter.
     *
     * @param ChronosDate|ChronosTime|DateTimeInterface|string|int|null $time
     * @param DateTimeZone|string|null $timezone
     * @return Chronos
     */
    function datetime(
        ChronosDate|ChronosTime|DateTimeInterface|string|int|null $time = 'now',
        DateTimeZone|string|null                                  $timezone = null
    ): Chronos
    {
        return new Chronos($time, $timezone);
    }
}

if (! function_exists('is_production')) {
    /**
     * Get the config value based on name.
     *
     * @return bool
     */
    function is_production(): bool
    {
        return Config::getInstance()->get('app.env') == 'production';
    }
}

if (! function_exists('dd')) {
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

if (! function_exists('bootstrapError')) {
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

if (! function_exists('errorLog')) {
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

        $file = __DIR__.'/../log/errors.log';
        file_put_contents($file, $message.PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

if (! function_exists('infoLog')) {
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

        $file = __DIR__.'/../log/info.log';
        file_put_contents($file, $message.PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

if (! function_exists('fullName')) {
    /**
     * Get full name based on first name and last name.
     *
     * @param string $firstName
     * @param string|null $lastName
     * @return string
     */
    function fullName(string $firstName, string $lastName = null): string
    {
        if (! $lastName) return $firstName;

        return "$firstName $lastName";
    }
}


if (! function_exists('randomStr')) {
    /**
     * Generate random string with length.
     *
     * @param int $length
     * @return string
     */
    function randomStr(int $length = 50): string
    {
        try {
            $random = bin2hex(random_bytes($length));
            if (strlen($random) > $length) {
                return substr($random, 0, $length);
            }

            return $random;
        } catch (Exception) {
            return md5(uniqid(rand().microtime(), true));
        }
    }
}
