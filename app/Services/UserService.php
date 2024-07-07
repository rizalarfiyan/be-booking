<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants;
use App\Repository\UserRepository;
use App\Repository\VerificationRepository;
use Booking\Constants as CoreConstants;
use Booking\Exception\BadRequestException;
use Booking\Exception\NotFoundException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Repository\BaseRepository;
use Lcobucci\JWT\Exception;
use MeekroDB;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use Throwable;

class UserService
{
    /** @var MeekroDB */
    protected MeekroDB $repo;

    /** @var UserRepository */
    protected UserRepository $user;

    /** @var VerificationRepository */
    protected VerificationRepository $verification;

    /**
     * @param BaseRepository $repo
     */
    public function __construct(BaseRepository $repo)
    {
        $this->repo = $repo->db();
        $this->user = new UserRepository($this->repo);
        $this->verification = new VerificationRepository($this->repo);
    }

    /**
     * @param $user
     * @param bool $isDetail
     * @return array
     */
    public static function response($user, bool $isDetail = false): array
    {
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

    /**
     * @throws UnprocessableEntitiesException
     */
    public function getAll($payload): array
    {
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

    /**
     * @throws UnprocessableEntitiesException
     * @throws NotFoundException
     */
    public function getById(int $userId): array
    {
        try {
            $data = $this->user->getById($userId);
        } catch (Throwable $e) {
            errorLog($e);

            throw new UnprocessableEntitiesException('Failed to get user. Please check your input.');
        }

        if (! $data) {
            throw new NotFoundException('User could not be found, please try again later.');
        }

        return self::response($data);
    }

    /**
     * @throws UnprocessableEntitiesException
     * @throws BadRequestException
     */
    public function create($payload): void
    {
        $payload['password'] = AuthService::hashPassword($payload['password']);

        try {
            $this->repo->startTransaction();
            $userId = $this->user->insertNewUser($payload);
            $hasSendEmail = $payload['status'] !== 'active';
            $code = randomStr();

            if ($hasSendEmail) {
                $verification = [
                    'userId' => $userId,
                    'type' => Constants::TYPE_VERIFICATION_ACTIVATION,
                    'code' => $code,
                    'expiredAt' => datetime()->addHours(1)->format('Y-m-d H:i:s'),
                ];
                $this->verification->insert($verification);
            }

            $this->repo->commit();
        } catch (Throwable $e) {
            $this->repo->rollback();
            errorLog($e);

            if ($e->getCode() === 1062) {
                throw new BadRequestException(CoreConstants::VALIDATION_MESSAGE, [
                    'email' => 'Email already exists.',
                ]);
            }

            throw new UnprocessableEntitiesException('User could not be created, please try again later.');
        }

        try {
            if (! $hasSendEmail) return;
            AuthService::sendVerification($payload, $code);
        } catch (PHPMailerException $e) {
            errorLog($e);
            throw new UnprocessableEntitiesException('Could not be sent email, please contact administrator.');
        }
    }

    /**
     * @param $payload
     * @throws UnprocessableEntitiesException
     */
    public function update($payload): void
    {
        try {
            $data = $this->user->getById($payload['userId']);
            if (! $data) {
                throw new NotFoundException('User could not be found, please try again later.');
            }

            $payload['password'] = isset($payload['password']) ? AuthService::hashPassword($payload['password']) : $data['password'];
            $this->user->updateUserDetails($payload);
        } catch (Throwable $e) {
            errorLog($e);

            throw new UnprocessableEntitiesException('User could not be updated, please try again later.');
        }
    }
}
