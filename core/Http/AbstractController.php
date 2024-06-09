<?php

declare(strict_types=1);

namespace Booking\Http;

use Booking\Response\JsonResponse;
use Booking\Response\Response;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractController implements ControllerInterface
{
    /**
     * Parse the data to array.
     *
     * @param ServerRequestInterface $request
     * @return array|null
     */
    public function parseRequestDataToArray(ServerRequestInterface $request): ?array
    {
        return json_decode($request->getBody()->getContents(), true);
    }

    /**
     * Get the current in query string
     * or return a default value.
     *
     * @param ServerRequestInterface $request
     * @return int
     */
    public function getCurrentPage(ServerRequestInterface $request): int
    {
        $queryParams = $request->getQueryParams();

        return isset($queryParams['page'])
            ? (int) $queryParams['page']
            : 1;
    }

    /**
     * Get the page size in query string
     * or return a default value.
     *
     * @param ServerRequestInterface $request
     * @return int
     */
    public function getPageSize(ServerRequestInterface $request): int
    {
        $queryParams = $request->getQueryParams();

        return isset($queryParams['per_page'])
            ? (int) $queryParams['per_page']
            : (int) config('app.page_size');
    }

    /**
     * Send response as json.
     *
     * @param array $content
     * @param int $code
     * @param array $headers
     * @return JsonResponse
     */
    protected function json(array $content, int $code = 200, array $headers = []): JsonResponse
    {
        return new JsonResponse($content, $code, $headers);
    }

    /**
     * @param mixed $data
     * @param int $code
     * @param ?string $message
     * @return JsonResponse
     */
    protected function sendJson(mixed $data = null, int $code = 200, string $message = null): JsonResponse
    {
        return $this->json([
            'status' => $code,
            'message' => $message ?? (new Response())->getReasonPhrase()[$code] ?? 'Unknown',
            'data' => $data,
        ], $code);
    }
}
