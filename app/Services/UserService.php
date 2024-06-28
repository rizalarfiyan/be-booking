<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\UserRepository;
use Booking\Constants as CoreConstants;
use Booking\Exception\BadRequestException;
use Booking\Exception\NotFoundException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Repository\BaseRepository;
use MeekroDB;
use Throwable;

class UserService
{
    /** @var MeekroDB */
    protected MeekroDB $repo;

    /** @var UserRepository */
    protected UserRepository $user;

    /**
     * @param BaseRepository $repo
     */
    public function __construct(BaseRepository $repo)
    {
        $this->repo = $repo->db();
        $this->user = new UserRepository($this->repo);
    }

    // response

    /**
     * @param $user
     * @param bool $isDetail
     * @return array
     */
    public static function response($user, bool $isDetail = false): array
    {


        // get all
        // semua kecuali password, created_at, updated_at

        $data = [
            'userId' => (int) $user['user_id'],
            'firstName' => $user['first_name'],
            'lastName' => $user['last_name'],
            'email' => $user['email'],
            'status' => $user['status'],
            'role' => $user['role'],
        ];

        if ($isDetail) {
            $data['points'] = (int) $user['points'];
            $data['bookCount'] = (int) $user['book_count'];
        }

        if (! $isDetail) {
            $data['createdAt'] = $user['created_at'];
            $data['updatedAt'] = $user['updated_at'];
        }

        return $data;

    }

    // get all users with pagination

    /**
     * @throws UnprocessableEntitiesException
     */
    public function getAll($payload): array
    {
        // get all
        // semua kecuali password, created_at, updated_at
        // return data, total data, total page, current page

        try {
            return [
                'content' => collect($this->user->getAll($payload))->map(fn ($user) => self::response($user, true)),
                'total' => $this->user->countAll($payload),
            ];
        } catch (Throwable $e) {
            errorLog($e);

            throw new UnprocessableEntitiesException('Users could not be found, please try again later.');
        }

    }

    // get user by id

    /**
     * @throws UnprocessableEntitiesException
     * @throws NotFoundException
     */
    public function getById(int $userId): mixed
    {
        // by id
        // return semua kecuali password, book_count, points
        try {
            $data = $this->user->getById($userId);
        } catch (Throwable $e) {
            errorLog($e);

            throw new UnprocessableEntitiesException('Failed to get user. Please check your input.');
        }

        if (! $data) {
            throw new NotFoundException('User could not be found, please try again later.');
        }

        return self::response($data, false);
    }

    // Create a new user
    /**
     * @throws UnprocessableEntitiesException
     * @throws BadRequestException
     */
    public function create($payload): void
    {

        // create user
        // payload =  email, password, first_name, last_name, status, role
        // return = success message

        try {
            $this->user->insert($payload);
        } catch (Throwable $e) {
            errorLog($e);

            if ($e->getCode() === 1062) {
                throw new BadRequestException(CoreConstants::VALIDATION_MESSAGE, [
                    'email' => 'Email already exists.',
                ]);
            }

            throw new UnprocessableEntitiesException('User could not be created, please try again later.');
        }

    }

    // Update user status

    /**
     * @param $payload
     * @throws UnprocessableEntitiesException
     * @throws NotFoundException
     */
    public function update($payload): void
    {
        // update user
        // success message
        // payload user_id, email, password, first_name, last_name, status, role

        try {
            $data = $this->user->getById($payload['userId']);
            if (! $data) {
                throw new NotFoundException('User could not be found, please try again later.');
            }
            $payload['password'] = $payload['password'] ?? $data['password'];
            $this->user->updateUserDetails($payload);


        } catch (Throwable $e) {
            errorLog($e);

            throw new UnprocessableEntitiesException('User could not be updated, please try again later.');
        }
    }
}
