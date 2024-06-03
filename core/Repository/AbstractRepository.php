<?php

declare(strict_types=1);

namespace Booking\Repository;

use MeekroDB;

abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var MeekroDB
     */
    public MeekroDB $db;

    public function __construct()
    {
        $this->init();
    }

    public function init(): void
    {
        $conf = config('db');
        $this->db = new MeekroDB($conf['host'], $conf['user'], $conf['password'], $conf['name'], $conf['port']);
    }
}
