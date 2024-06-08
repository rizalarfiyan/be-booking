<?php

declare(strict_types=1);

namespace Booking\Repository;

use MeekroDB;

abstract class AbstractRepository
{
    /**
     * @var MeekroDB
     */
    public MeekroDB $db;

    public function __construct()
    {
        $this->init();
    }

    /**
     * Initialize the database connection.
     *
     * @return void
     */
    protected function init(): void
    {
        $conf = config('db');
        $this->db = new MeekroDB($conf['host'], $conf['user'], $conf['password'], $conf['name'], $conf['port']);
    }

    /**
     * Get meekrodb instance.
     *
     * @return MeekroDB
     */
    public function db(): MeekroDB
    {
        return $this->db;
    }
}
