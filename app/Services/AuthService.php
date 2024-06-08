<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants;
use Booking\Constants as CoreConstants;
use App\Repository\UserRepository;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Mailer;
use PHPMailer\PHPMailer\Exception;
use Throwable;

class AuthService
{
    /** @var UserRepository */
    protected UserRepository $user;

    /**
     * @param UserRepository $user
     */
    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    /**
     * @param $data
     * @return void
     * @throws UnprocessableEntitiesException
     */
    public function register($data): void
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, [
            'cost' => 12,
        ]);

        try {
            $code = randomStr();
            $this->user->db()->startTransaction();
            $userId = $this->user->insert($data);

            $verification = [
                'user_id' => $userId,
                'type' => Constants::TYPE_VERIFICATION_ACTIVATION,
                'code' => $code,
            ];
            $this->user->insertVerifications($verification);
            $this->user->db()->commit();
        } catch (Throwable $e) {
            $this->user->db()->rollback();
            errorLog($e);

            if ($e->getCode() === 1062) {
                throw new UnprocessableEntitiesException(CoreConstants::VALIDATION_MESSAGE, [
                    'email' => 'Email already exists.',
                ]);
            }

            throw new UnprocessableEntitiesException('User could not be created, please contact administrator.');
        }

        try {
            $fullName = fullName($data['first_name'], $data['last_name']);
            // TODO: update the url later!
            $url = 'http://localhost:8000/verify-email?email=' . $code;

            $mailer = new Mailer();
            $mail = $mailer->getMail();
            $mail->addAddress($data['email'], $fullName);
            $mail->isHTML();
            $mail->Subject = 'Email Verification';
            $mail->Body = $mailer->getTemplate('verification', [
                'name' => $fullName,
                'url' => $url,
            ]);
            $mail->AltBody = "Hi $fullName, Please verify your email by clicking the link below: $url";
            $mail->send();
        } catch (Exception $e) {
            errorLog($e);
            throw new UnprocessableEntitiesException('Message could not be sent, please contact administrator.');
        }
    }
}
