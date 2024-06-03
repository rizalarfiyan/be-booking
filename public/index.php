<?php

declare(strict_types=1);

use Booking\Application;
use Symfony\Component\Dotenv\Dotenv;

try {
    require_once __DIR__.'/../vendor/autoload.php';

    $dotenv = new Dotenv();
    $dotenv->usePutenv();
    $dotenv->bootEnv(__DIR__.'/../.env');

    date_default_timezone_set(config('app.timezone'));

    infoLog('Apps running!');
    $apps = new Application();
    $apps->run();
} catch (Throwable $t) {
    errorLog($t);
    bootstrapError($t);
}
