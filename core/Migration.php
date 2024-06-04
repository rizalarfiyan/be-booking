<?php

namespace Booking;

use ByJG\DbMigration\Database\MySqlDatabase;
use ByJG\DbMigration\Exception\DatabaseDoesNotRegistered;
use ByJG\DbMigration\Exception\DatabaseIsIncompleteException;
use ByJG\DbMigration\Exception\DatabaseNotVersionedException;
use ByJG\DbMigration\Exception\InvalidMigrationFile;
use ByJG\DbMigration\Exception\OldVersionSchemaException;
use ByJG\DbMigration\Migration as DBMigration;
use ByJG\Util\Uri;
use Exception;

class Migration
{
    /**
     * Migration directory.
     *
     * @var string
     */
    protected string $path = __DIR__.'/../migrations';

    /**
     * Migration connection url.
     *
     * @var string
     */
    protected string $connection;

    /**
     * @var string
     */
    protected string $migrationTable = 'migrations';

    /**
     * @var DBMigration
     */
    protected DBMigration $migration;

    /**
     * @var int
     */
    protected int $version;

    /**
     * @param $dir
     * @param $connection
     * @throws InvalidMigrationFile
     */
    public function __construct($dir = null, $connection = null)
    {
        if ($dir) {
            $this->path = $dir;
        }

        if (! $connection) {
            $conf = config('db');
            $this->connection = "mysql://{$conf['user']}:{$conf['password']}@{$conf['host']}:{$conf['port']}/{$conf['name']}";
        }

        $this->initialize();
    }

    /**
     * @return void
     * @throws InvalidMigrationFile
     */
    private function initialize(): void
    {
        $uri = new Uri($this->connection);
        DBMigration::registerDatabase(MySqlDatabase::class);
        $this->migration = new DBMigration($uri, $this->path, true, $this->migrationTable);
    }

    /**
     * @param string $path
     * @param int $startVersion
     * @return int
     */
    protected function createMigrationSql(string $path, int $startVersion): int
    {
        $files = glob("$path/*.sql");
        $lastVersion = $startVersion;
        foreach ($files as $file) {
            $version = intval(basename($file));
            if ($version > $lastVersion) {
                $lastVersion = $version;
            }
        }

        $lastVersion = $lastVersion + 1;

        file_put_contents(
            "$path/".str_pad($lastVersion, 5, '0', STR_PAD_LEFT).'.sql',
            "-- Migrate to Version $lastVersion \n\n"
        );

        return $lastVersion;
    }

    /**
     * @return void
     */
    protected function execute(): void
    {
        $this->migration->addCallbackProgress(function ($command, $version) {
            echo "Doing: $command to $version\n";
        });
    }

    /**
     * @param string[] $argv
     * @throws Exception
     */
    public function parseVersion(array $argv = []): void
    {
        $upTo = $argv[2] ?? null;
        $version = intval($upTo);
//        if (!$upTo || !is_numeric($upTo) || $version < 0) {
//            echo "[WARNING] Please provide version to reset!\n";
//            echo "[INFO] Example: composer composer run migration 1\n";
//            echo "[INFO] The 1 is a number to install migration\n";
//
//            throw new Exception("Invalid version number");
//        }

        $this->version = $version;
    }

    /**
     * @return void
     * @throws DatabaseDoesNotRegistered
     * @throws DatabaseNotVersionedException
     * @throws OldVersionSchemaException
     */
    public function install(): void
    {
        $this->execute();

        $action = 'Database is already versioned.';
        try {
            $this->migration->getCurrentVersion();
        } catch (DatabaseNotVersionedException) {
            $action = 'Created the version table';
            $this->migration->createVersion();
        } catch (OldVersionSchemaException) {
            $action = 'Updated the version table';
            $this->migration->updateTableVersion();
        }

        $version = $this->migration->getCurrentVersion();
        echo "$action\n";
        echo "current version : {$version['version']}\n";
        echo "current status : {$version['status']}\n";
    }

    /**
     * @return void
     * @throws DatabaseDoesNotRegistered
     * @throws DatabaseIsIncompleteException
     * @throws DatabaseNotVersionedException
     * @throws InvalidMigrationFile
     * @throws OldVersionSchemaException
     */
    public function reset(): void
    {
        $this->execute();
        $this->migration->prepareEnvironment();
        $this->migration->reset($this->version);
    }

    /**
     * Create migration files.
     *
     * @return void
     */
    public function create(): void
    {
        if (! file_exists($this->path)) {
            mkdir($this->path, 0777, true);
        }

        if (! file_exists("$this->path/base.sql")) {
            file_put_contents("$this->path/base.sql", '-- Put here your base SQL');
        }

        if (! file_exists("$this->path/migrations")) {
            mkdir("$this->path/migrations", 0777, true);
            mkdir("$this->path/migrations/up", 0777, true);
            mkdir("$this->path/migrations/down", 0777, true);
        }

        echo "Created UP version: {$this->createMigrationSql("$this->path/migrations/up", 0)}\n";
        echo "Created DOWN version: {$this->createMigrationSql("$this->path/migrations/down", -1)}\n";
    }

    /**
     * @throws DatabaseDoesNotRegistered
     * @throws DatabaseIsIncompleteException
     * @throws DatabaseNotVersionedException
     * @throws InvalidMigrationFile
     * @throws OldVersionSchemaException
     */
    public function up(): void
    {
        $this->execute();
        $this->migration->up($this->version, true);
    }

    /**
     * @return void
     * @throws DatabaseDoesNotRegistered
     * @throws DatabaseIsIncompleteException
     * @throws DatabaseNotVersionedException
     * @throws InvalidMigrationFile
     * @throws OldVersionSchemaException
     */
    public function down(): void
    {
        $this->execute();
        $this->migration->down($this->version, true);
    }

    /**
     * @throws DatabaseDoesNotRegistered
     * @throws DatabaseIsIncompleteException
     * @throws DatabaseNotVersionedException
     * @throws InvalidMigrationFile
     * @throws OldVersionSchemaException
     */
    public function update(): void
    {
        $this->execute();
        $this->migration->update($this->version, true);
    }

    /**
     * @return void
     * @throws DatabaseDoesNotRegistered
     * @throws DatabaseNotVersionedException
     * @throws OldVersionSchemaException
     */
    public function version(): void
    {
        $this->execute();
        $version = $this->migration->getCurrentVersion();
        echo "current version : {$version['version']}\n";
        echo "current status : {$version['status']}\n";
    }
}
