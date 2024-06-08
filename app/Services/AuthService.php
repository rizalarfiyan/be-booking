<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants;
use App\Repository\UserRepository;
use Booking\Constants as CoreConstants;
use Booking\Exception\BadRequestException;
use Booking\Exception\UnauthorizedException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Mailer;
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
use PHPMailer\PHPMailer\Exception;
use Psr\Http\Message\ServerRequestInterface;
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
     * @return string
     */
    public static function generateToken(int $id, string $role): string
    {
        $conf = config('jwt');
        $issuedBy = config('app.url');

        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $algorithm = new Sha256();
        $signingKey = InMemory::plainText($conf['secret']);

        $token = $tokenBuilder
            ->issuedBy($issuedBy)
            ->identifiedBy($conf['jti'])
            ->withClaim('id', $id)
            ->withClaim('role', $role)
            ->issuedAt(datetime()->toNative())
            ->expiresAt(datetime()->addSeconds($conf['ttl'])->toNative())
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
        if (! $authorization) {
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

        if (! $token->isIdentifiedBy($conf['jti']) || ! $token->hasBeenIssuedBy($issuedBy)) {
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
            'user_id' => (int) $user['user_id'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'points' => (int) $user['points'],
            'book_count' => (int) $user['book_count'],
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
                throw new BadRequestException(CoreConstants::VALIDATION_MESSAGE, [
                    'email' => 'Email already exists.',
                ]);
            }

            throw new UnprocessableEntitiesException('User could not be created, please contact administrator.');
        }

        try {
            $fullName = fullName($data['first_name'], $data['last_name']);
            // TODO: update the url later!
            $url = 'http://localhost:8000/verify-email?email='.$code;

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

    /**
     * @param $data
     * @return array
     * @throws BadRequestException
     */
    public function login($data): array
    {
        $user = $this->user->getByEmail($data['email']);

        if (! $user || ! self::checkPassword($data['password'], $user['password'])) {
            throw new BadRequestException(CoreConstants::VALIDATION_MESSAGE, [
                'email' => 'Email or password is incorrect.',
            ]);
        }

        if ($user['status'] === Constants::TYPE_USER_INACTIVE) {
            throw new BadRequestException(CoreConstants::VALIDATION_MESSAGE, [
                'email' => 'Email is not verified.',
            ]);
        }

        $token = self::generateToken((int) $user['user_id'], $user['role']);

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
}
