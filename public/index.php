<?php

declare(strict_types=1);

try {
    require_once __DIR__ . '/../vendor/autoload.php';
    infoLog("Apps running!");
    echo "Hello World!";
} catch (Throwable $t) {
    errorLog($t);
    bootstrapError($t);
}
