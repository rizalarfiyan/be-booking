<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\ContactRepository;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Repository\BaseRepository;
use MeekroDB;
use Throwable;

class ContactService
{
    /** @var MeekroDB */
    protected MeekroDB $repo;

    /** @var ContactRepository */
    protected ContactRepository $user;

    /**
     * @param BaseRepository $repo
     */
    public function __construct(BaseRepository $repo)
    {
        $this->repo = $repo->db();
        $this->user = new ContactRepository($this->repo);
    }

    /**
     * Insert submitted contact form.
     *
     * @param $payload
     * @return void
     * @throws UnprocessableEntitiesException
     */
    public function newContact($payload): void
    {
        try {
            $this->user->insert($payload);
        } catch (Throwable $t) {
            errorLog($t);
            throw new UnprocessableEntitiesException('Failed to send contact.');
        }
    }
}
