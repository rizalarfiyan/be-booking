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
     * @param $value
     * @param $default
     * @return int|null
     */
    public function parseNumeric($value, $default = null): int|null
    {
        return isset($value) ? is_numeric($value) ? (int)$value : $default : $default;
    }

    /**
     * Get the datatable schema in query string
     * or return a default value.
     *
     * @param ServerRequestInterface $request
     * @return array
     */
    public function getDatatable(ServerRequestInterface $request): array
    {
        $query = $request->getQueryParams();
        $count = $this->parseNumeric($query['count']);
        $page = $this->parseNumeric($query['count'], 0);

        return [
            'page' => $page > 0 ? $page - 1 : 0,
            'count' => !empty($count) ? $count : (int)config('app.count'),
            'orderBy' => $query['orderBy'] ?? null,
            'orderType' => $query['orderType'] ?? null,
            'search' => $query['search'] ?? null,
        ];
    }

    /**
     * Array response for list data.
     *
     * @param array $content
     * @param array $metadata
     * @param int $total
     * @return array
     */
    public function listResponse(array $content, array $metadata, int $total = 0): array
    {
        $total = $content['total'];

        return [
            'content' => $content['content'],
            'metadata' => [
                'total' => $total,
                'page' => $metadata['page'] + 1,
                'perPage' => $metadata['count'],
                'totalPage' => ceil($total / $metadata['count']),
            ],
        ];
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
