<?php

declare(strict_types=1);

use Booking\Application;

try {
    require_once __DIR__ . '/../vendor/autoload.php';
    infoLog("Apps running!");
    $apps = new Application();
    $apps->run();
} catch (Throwable $t) {
    errorLog($t);
    bootstrapError($t);
}
