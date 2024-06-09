<?php

declare(strict_types=1);

namespace Booking\Repository;

use MeekroDB;

class BaseRepository
{
    /**
     * @var MeekroDB
     */
    public MeekroDB $db;

    public function __construct(MeekroDB $db = null)
    {
        if ($db) {
            $this->db = $db;

            return;
        }

        $conf = config('db');
        $this->db = new MeekroDB($conf['host'], $conf['user'], $conf['password'], $conf['name'], $conf['port']);
        if (! is_production()) {
            $this->db->logfile = __DIR__.'/../../log/db.log';
        }
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
