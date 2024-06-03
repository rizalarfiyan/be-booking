<?php

declare(strict_types=1);

namespace Booking;

use DirectoryIterator;
use SplFileInfo;

class Config
{
    /** @var string */
    public const PATH = __DIR__.'/../config';

    /** @var Config */
    private static Config $instance;

    /** @var array */
    private static array $data = [];

    /**
     * @return Config
     */
    public static function getInstance(): self
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get the value of config.
     *
     * @param  string $key
     * @param mixed|null $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (! self::$data) {
            self::$instance->loadData();
        }

        $parts = explode('.', $key);

        $pointer = self::$data;
        while ($part = array_shift($parts)) {
            if (! array_key_exists($part, $pointer)) {
                return $default;
            }

            $pointer = $pointer[$part];
        }

        return $pointer;
    }

    /**
     * @return void
     */
    private function loadData(): void
    {
        $iterator = new DirectoryIterator(realpath(self::PATH));
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot() || $fileInfo->isDir()) {
                continue;
            }

            list($pathname, $filename) = $this->getPathAndFileName($fileInfo);

            self::$data[$filename] = require $pathname;
        }
    }

    /**
     * @param  SplFileInfo $fileInfo
     * @return array
     */
    private function getPathAndFileName(SplFileInfo $fileInfo): array
    {
        return [$fileInfo->getPathname(), substr($fileInfo->getFilename(), 0, -4)];
    }
}
