<?php

declare(strict_types=1);

namespace Booking;

use Booking\Exception\ExceptionHandler;
use Booking\Middleware\MiddlewarePipe;
use Booking\Middleware\RouteProcessor;
use Booking\Router\Router;
use DI\ContainerBuilder;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class Application
{
    /** @var ContainerInterface */
    private ContainerInterface $container;

    /** @var Router */
    private Router $router;

    /** @var Config */
    private Config $config;

    /**
     * Application constructor.
     * @param string|null $routes
     * @throws Exception
     */
    public function __construct(string $routes = null)
    {
        $this->config = Config::getInstance();
        $this->container = $this->makeContainer();
        $this->router = new Router($routes);
    }

    /**
     * Run the application and emit the response.
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function run(): void
    {
        try {
            $pipeline = new MiddlewarePipe();

            // Putting middlewares in pipeline
            $middlewares = $this->config->get('middlewares', []);
            foreach ($middlewares as $middleware) {
                $pipeline->pipe(new $middleware());
            }

            $request = $this->container->get('request');

            // Getting requested route details
            $routes = $this->router->getRoutes($request);

            // Putting middlewares for the found route in pipeline
            foreach ($routes['middlewares'] as $middleware) {
                $pipeline->pipe(new $middleware());
            }

            // Processing route
            $pipeline->pipe(new RouteProcessor($this->container, $routes));

            $response = $pipeline->handle($request);
        } catch (Throwable $t) {
            errorLog($t);
            $request = $this->container->get('request');
            $response = ExceptionHandler::handle($t, $request, is_production());
        }

        $this->container->get('emitter')->emit($response);
    }

    /**
     * Make the DI container.
     *
     * @return ContainerInterface
     * @throws Exception
     */
    private function makeContainer(): ContainerInterface
    {
        if (is_production()) {
            return (new ContainerBuilder())
                ->enableCompilation($this->config->get('app.cache_dir'))
                ->addDefinitions($this->config->get('di'))
                ->build();
        }

        return (new ContainerBuilder())
            ->addDefinitions($this->config->get('di'))
            ->build();
    }
}
