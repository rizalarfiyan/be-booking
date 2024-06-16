<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\ContactRepository;
use Booking\Exception\NotFoundException;
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
     * @param $contact
     * @param bool $withMessage
     * @return array
     */
    public static function response($contact, bool $withMessage = false): array
    {
        $data = [
            'contactId' => (int) $contact['contact_id'],
            'firstName' => $contact['first_name'],
            'lastName' => $contact['last_name'] ?? '',
            'email' => $contact['email'],
            'phone' => $contact['phone'],
            'submittedAt' => $contact['created_at'],
        ];

        if ($withMessage) {
            $data['message'] = $contact['message'];
        }

        return $data;
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

    /**
     * Get all contacts.
     *
     * @param $payload
     * @return array
     * @throws UnprocessableEntitiesException
     */
    public function getAll($payload): array
    {
        try {
            return [
                'content' => collect($this->user->getAll($payload))->map(fn ($contact) => self::response($contact)),
                'total' => $this->user->countAll($payload),
            ];
        } catch (Throwable $t) {
            errorLog($t);
            throw new UnprocessableEntitiesException('Failed to get all contacts.');
        }
    }

    /**
     * Get contact detail.
     *
     * @param int $id
     * @return array
     * @throws UnprocessableEntitiesException
     * @throws NotFoundException
     */
    public function getDetail(int $id): array
    {
        try {
            $data = $this->user->getById($id);
        } catch (Throwable $t) {
            errorLog($t);
            throw new NotFoundException('Failed to get all contacts.');
        }

        if (! $data) {
            throw new UnprocessableEntitiesException('Contact not found.');
        }

        return self::response($data, true);
    }
}
