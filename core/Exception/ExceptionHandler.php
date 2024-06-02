<?php

namespace Booking\Exception;

use Booking\Config;
use Booking\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\ValidationException;
use Throwable;

class ExceptionHandler
{
    /**
     * @param Throwable $t
     * @param ServerRequestInterface $request
     * @param bool $isProd
     * @return ResponseInterface
     */
    public static function handle(
        Throwable $t,
        ServerRequestInterface $request,
        bool $isProd
    ): ResponseInterface {
        switch ($t) {
            case $t instanceof NotFoundException:
            case $t instanceof NotFoundHttpException:
                $data = [
                    'type'      => get_class($t),
                    'message'   => $t->getMessage(),
                    'code'      => 404,
                ];

                break;
            case $t instanceof NotAllowedHttpException:
                $data = [
                    'type'      => get_class($t),
                    'message'   => $t->getMessage(),
                    'code'      => 405,
                ];

                break;
            case $t instanceof UnauthorizedException:
                $data = [
                    'type'      => get_class($t),
                    'message'   => $t->getMessage(),
                    'code'      => 401,
                ];

                break;
            case $t instanceof ValidationException:
                $data = [
                    'type'      => get_class($t),
                    'message'   => (function ($t) {
                        $messages = explode('- ', $t->getFullMessage());
                        $messages = array_unique(array_filter($messages));
                        $messages = array_values(array_map('trim', $messages));
                        return $messages;
                    })($t),
                    'code'      => 422,
                ];

                break;

            default:
                $data = [
                    'type'      => get_class($t),
                    'message'   => $t->getMessage(),
                    'code'      => 500,
                ];

                break;
        }

        if (!$isProd && $data['code'] != 422) {
            $data['trace'] = $t->getTrace();
        }

        return self::sendResponse($data, $data['code']);
    }

    /**
     * @param array $data
     * @param int $code
     * @return ResponseInterface
     */
    private static function sendResponse(array $data = [], int $code = 500): ResponseInterface
    {
        $headers = (Config::getInstance())->get('cors');

        return new JsonResponse($data, $code, $headers ?? []);
    }
}
