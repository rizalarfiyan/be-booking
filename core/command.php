<?php

declare(strict_types=1);

use Booking\Migration;
use Symfony\Component\Dotenv\Dotenv;

try {
    require_once __DIR__.'/../vendor/autoload.php';

    $dotenv = new Dotenv();
    $dotenv->usePutenv();
    $dotenv->bootEnv(__DIR__.'/../.env');

    $action = $argv[1] ?? '';
    switch ($action) {
        case 'migration:install':
            echo "[INFO] Running Install Migration...\n";
            (new Migration())->install();
            echo "[SUCCESS] Install Migration...\n";
            break;
        case 'migration:reset':
            echo "[INFO] Running Reset Migration...\n";
            $migration = new Migration();
            $migration->parseVersion($argv);
            $migration->reset();
            echo "[SUCCESS] Reset Migration...\n";
            break;
        case 'migration:create':
            echo "[INFO] Running Create Migration file...\n";
            (new Migration())->create();
            echo "[SUCCESS] Create Migration file...\n";
            break;
        case 'migration:up':
            echo "[INFO] Running Up Migration...\n";
            $migration = new Migration();
            $migration->parseVersion($argv);
            $migration->up();
            echo "[SUCCESS] Up Migration...\n";
            break;
        case 'migration:down':
            echo "[INFO] Running Down Migration...\n";
            $migration = new Migration();
            $migration->parseVersion($argv);
            $migration->down();
            echo "[SUCCESS] Down Migration...\n";
            break;
        case 'migration:update':
            echo "[INFO] Running Update Migration...\n";
            $migration = new Migration();
            $migration->parseVersion($argv);
            $migration->update();
            echo "[SUCCESS] Update Migration...\n";
            break;
        case 'migration:version':
            echo "[INFO] Running Version Migration...\n";
            (new Migration())->version();
            echo "[SUCCESS] Version Migration...\n";
            break;
        default:
            echo "[WARNING] Invalid action!\n";
            break;
    }
} catch (Throwable $t) {
    echo "[ERROR] {$t->getMessage()}\n";
}
