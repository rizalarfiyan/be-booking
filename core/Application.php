<?php

declare(strict_types=1);

namespace Booking;

use Booking\Router\Router;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Throwable;

class Application
{
    /** @var ?ContainerInterface */
    private ?ContainerInterface $container;

    /** @var Router */
    private Router $router;

    /** @var Config */
    private Config $config;

    /**
     * Application constructor.
     * @param string|null $routes
     */
    public function __construct(string $routes = null)
    {
        $this->config = Config::getInstance();
        $this->container = $this->makeContainer();
        $this->router = new Router($routes);
    }

    /**
     * Run the application and emit the response
     *
     * @return void
     */
    public function run()
    {
        // TODO: Implement the run method
    }

    /**
     * Make the DI container
     *
     * @return ?ContainerInterface
     */
    private function makeContainer(): ?ContainerInterface
    {
        try {
            if (is_production()) {
                return (new ContainerBuilder())
                    ->enableCompilation($this->config->get('app.cache_dir'))
                    ->addDefinitions($this->config->get('di'))
                    ->build();
            }

            return (new ContainerBuilder())
                ->addDefinitions($this->config->get('di'))
                ->build();
        } catch (Throwable $t) {
            errorLog($t);
            bootstrapError($t);
        }
    }
}
