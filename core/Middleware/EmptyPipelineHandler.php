<?php
/**
 * @see       https://github.com/zendframework/zend-stratigility for the canonical source repository
 * @copyright Copyright (c) 2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-stratigility/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Booking\Middleware;

use Booking\Exception\EmptyPipelineException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class EmptyPipelineHandler implements RequestHandlerInterface
{
    /**
     * @var string
     */
    private string $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        throw EmptyPipelineException::forClass($this->className);
    }
}
