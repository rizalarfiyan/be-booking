<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants;
use App\Repository\UserRepository;
use App\Repository\VerificationRepository;
use Booking\Constants as CoreConstants;
use Booking\Exception\BadRequestException;
use Booking\Exception\UnauthorizedException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Mailer;
use Booking\Repository\BaseRepository;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\HasClaim;
use Lcobucci\JWT\Validation\Constraint\IdentifiedBy;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Validator;
use MeekroDB;
use PHPMailer\PHPMailer\Exception;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class AuthService
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
     * Has password with bcrypt.
     *
     * @param string $password
     * @return string
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, [
            'cost' => 12,
        ]);
    }

    /**
     * Check.
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function checkPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Generate JWT Token.
     *
     * @param int $id
     * @param string $role
     * @param bool $isRemember
     * @return string
     */
    public static function generateToken(int $id, string $role, bool $isRemember = false): string
    {
        $conf = config('jwt');
        $issuedBy = config('app.url');

        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $algorithm = new Sha256();
        $signingKey = InMemory::plainText($conf['secret']);

        $ttl = $conf['ttl'];
        if ($isRemember) $ttl *= 2;

        $token = $tokenBuilder
            ->issuedBy($issuedBy)
            ->identifiedBy($conf['jti'])
            ->withClaim('id', $id)
            ->withClaim('role', $role)
            ->issuedAt(datetime()->toNative())
            ->expiresAt(datetime()->addSeconds($ttl)->toNative())
            ->getToken($algorithm, $signingKey);

        return $token->toString();
    }

    /**
     * Get Auth JWT Token.
     *
     * @param ServerRequestInterface $request
     * @return Token
     * @throws UnauthorizedException
     */
    public static function getAuthToken(ServerRequestInterface $request): Token
    {
        $authorization = $request->getHeaderLine('Authorization');
        if (!$authorization) {
            throw new UnauthorizedException('Token not present');
        }

        $authorizationToken = explode(' ', $authorization);
        if (count($authorizationToken) !== 2 || $authorizationToken[0] !== 'Bearer') {
            throw new UnauthorizedException('Invalid token');
        }

        try {
            $token = (new Parser(new JoseEncoder()))->parse($authorizationToken[1]);
        } catch (Throwable) {
            throw new UnauthorizedException('Invalid token');
        }

        return $token;
    }

    /**
     * @param ServerRequestInterface $request
     * @return int
     * @throws UnauthorizedException
     */
    public static function getUserIdFromToken(ServerRequestInterface $request): int
    {
        $token = self::getAuthToken($request);

        return $token->claims()->get('id');
    }


    /**
     * @param ServerRequestInterface $request
     * @return ?int
     */
    public static function getUserIdFromTokenIfAvailable(ServerRequestInterface $request): ?int
    {
        try {
            $token = self::getAuthToken($request);
            return $token->claims()->get('id');
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     * @throws UnauthorizedException
     */
    public static function getRoleFromToken(ServerRequestInterface $request): string
    {
        $token = self::getAuthToken($request);

        return $token->claims()->get('role');
    }

    /**
     * Validate JWT Token.
     *
     * @param Token $token
     * @return void
     * @throws UnauthorizedException
     */
    public static function validateToken(Token $token): void
    {
        $conf = config('jwt');
        $issuedBy = config('app.url');

        $constraints = [
            new SignedWith(new Sha256(), InMemory::plainText($conf['secret'])),
            new HasClaim('id'),
            new HasClaim('role'),
            new IdentifiedBy($conf['jti']),
        ];

        $validator = new Validator();
        try {
            $validator->assert($token, ...$constraints);
        } catch (Throwable $e) {
            errorLog($e);
            throw new UnauthorizedException('Invalid token');
        }

        if (!$token->isIdentifiedBy($conf['jti']) || !$token->hasBeenIssuedBy($issuedBy)) {
            throw new UnauthorizedException('Token not mismatched');
        }

        if ($token->isExpired(datetime()->toNative())) {
            throw new UnauthorizedException('Token expired');
        }
    }

    /**
     * @param $user
     * @return array
     */
    public static function userResponse($user): array
    {
        return [
            'userId' => (int)$user['user_id'],
            'firstName' => $user['first_name'],
            'lastName' => $user['last_name'] ?? '',
            'email' => $user['email'],
            'role' => $user['role'],
            'avatar' => '',
            'points' => (int)$user['points'],
            'bookCount' => (int)$user['book_count'],
        ];
    }

    /**
     * @param $data
     * @return void
     * @throws UnprocessableEntitiesException
     * @throws BadRequestException
     */
    public function register($data): void
    {
        $data['password'] = self::hashPassword($data['password']);

        try {
            $code = randomStr();
            $this->repo->startTransaction();
            $userId = $this->user->insert($data);
            $verification = [
                'userId' => $userId,
                'type' => Constants::TYPE_VERIFICATION_ACTIVATION,
                'code' => $code,
                'expiredAt' => datetime()->addHours(1)->format('Y-m-d H:i:s'),
            ];
            $this->verification->insert($verification);
            $this->repo->commit();
        } catch (Throwable $e) {
            $this->repo->rollback();
            errorLog($e);

            if ($e->getCode() === 1062) {
                throw new BadRequestException(CoreConstants::VALIDATION_MESSAGE, [
                    'email' => 'Email already exists.',
                ]);
            }

            throw new UnprocessableEntitiesException('User could not be created, please contact administrator.');
        }

        try {
            self::sendVerification($data, $code);
        } catch (Exception $e) {
            errorLog($e);
            throw new UnprocessableEntitiesException('Could not be sent email, please contact administrator.');
        }
    }

    /**
     * @throws Exception
     */
    public static function sendVerification($payload, $code): void
    {
        $fullName = fullName($payload['firstName'], $payload['lastName']);
        $url = config('url.activation').'?code='.$code;

        $mailer = new Mailer();
        $mail = $mailer->getMail();
        $mail->addAddress($payload['email'], $fullName);
        $mail->isHTML();
        $mail->Subject = 'Email Verification';
        $mail->Body = $mailer->getTemplate('verification', [
            'name' => $fullName,
            'url' => $url,
            'image' => config('url.fe').'/images/email/verification.png',
        ]);
        $mail->AltBody = "Hi $fullName, Please verify your email by clicking the link below: $url";
        $mail->send();
    }

    /**
     * @param $data
     * @return array
     * @throws BadRequestException
     * @throws UnprocessableEntitiesException
     */
    public function login($data): array
    {
        $user = $this->user->getByEmail($data['email']);

        if (! $user || ! self::checkPassword($data['password'], $user['password']) || $user['status'] === Constants::TYPE_USER_BANNED) {
            throw new BadRequestException(CoreConstants::VALIDATION_MESSAGE, [
                'email' => 'Email or password is incorrect.',
            ]);
        }

        if ($user['status'] === Constants::TYPE_USER_INACTIVE) {
            throw new UnprocessableEntitiesException('Email is not verified. Please check your email and verify.');
        }

        $token = self::generateToken((int)$user['user_id'], $user['role'], $data['isRemember']);

        return [
            'token' => $token,
            'user' => self::userResponse($user),
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function me($id): array
    {
        $user = $this->user->getById($id);

        return self::userResponse($user);
    }

    /**
     * check if verification is not valid.
     *
     * @param $data
     * @param string $type
     * @return bool
     */
    protected function isNotValidVerification($data, string $type): bool
    {
        return !$data || $data['type'] !== $type || datetime($data['expired_at'])->isPast();
    }

    /**
     * @param string $code
     * @return void
     * @throws BadRequestException
     * @throws UnprocessableEntitiesException
     */
    public function activation(string $code): void
    {
        $data = $this->verification->getByCode($code);
        if ($this->isNotValidVerification($data, Constants::TYPE_VERIFICATION_ACTIVATION)) {
            throw new BadRequestException('Invalid activation code.');
        }

        try {
            $userId = (int)$data['user_id'];
            $this->repo->startTransaction();
            $this->user->updateStatus(Constants::TYPE_USER_ACTIVE, $userId);
            $this->verification->deleteByTypeAndUser(Constants::TYPE_VERIFICATION_ACTIVATION, $userId);
            $this->repo->commit();
        } catch (Throwable $e) {
            $this->repo->rollback();
            errorLog($e);
            throw new UnprocessableEntitiesException('User could not be activated, please contact administrator.');
        }
    }

    /**
     * @param string $email
     * @return void
     * @throws UnprocessableEntitiesException
     */
    public function forgotPassword(string $email): void
    {
        $user = $this->user->getByEmail($email);
        if (!$user) {
            return;
        }

        $code = randomStr();
        $verification = [
            'userId' => (int)$user['user_id'],
            'type' => Constants::TYPE_VERIFICATION_FORGOT_PASSWORD,
            'code' => $code,
            'expiredAt' => datetime()->addHours(1)->format('Y-m-d H:i:s'),
        ];
        $this->verification->insert($verification);

        try {
            self::sendForgotPassword($user, $code);
        } catch (Exception $e) {
            errorLog($e);
            throw new UnprocessableEntitiesException('Could not be sent email, please contact administrator.');
        }
    }

    /**
     * @throws Exception
     */
    public static function sendForgotPassword($user, $code): void
    {
        $fullName = fullName($user['first_name'], $user['last_name']);
        $url = config('url.change_password').'?code='.$code;

        $mailer = new Mailer();
        $mail = $mailer->getMail();
        $mail->addAddress($user['email'], $fullName);
        $mail->isHTML();
        $mail->Subject = 'Reset Password';
        $mail->Body = $mailer->getTemplate('reset_password', [
            'name' => $fullName,
            'url' => $url,
            'image' => config('url.fe').'/images/email/reset_password.png',
        ]);
        $mail->AltBody = "Hi $fullName, Please reset your password by clicking the link below: $url";
        $mail->send();
    }

    /**
     * @param $data
     * @return void
     * @throws UnprocessableEntitiesException
     */
    public function changePassword($data): void
    {
        $code = $data['code'];
        $password = $data['password'];

        $data = $this->verification->getByCode($code);
        if ($this->isNotValidVerification($data, Constants::TYPE_VERIFICATION_FORGOT_PASSWORD)) {
            throw new UnprocessableEntitiesException('Invalid change password code. Please check email and try again.');
        }

        try {
            $userId = (int)$data['user_id'];
            $this->repo->startTransaction();
            $this->user->updatePassword(self::hashPassword($password), $userId);
            $this->verification->deleteByTypeAndUser(Constants::TYPE_VERIFICATION_FORGOT_PASSWORD, $userId);
            $this->repo->commit();
        } catch (Throwable $e) {
            $this->repo->rollback();
            errorLog($e);
            throw new UnprocessableEntitiesException('Password could not be changed, please contact administrator.');
        }
    }
}
