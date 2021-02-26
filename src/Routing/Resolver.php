<?php

declare(strict_types=1);

namespace Framework\Routing;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

class Resolver
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Resolve route's middleswares and handle
     *
     * @param Route $route
     * @return Route
     */
    public function resolve(Route $route): Route
    {
        $this->resolveMiddlewares($route)
            ->resolveHandler($route);

        return $route;
    }

    /**
     * Resolve the middlewares
     *
     * @return self
     */
    private function resolveMiddlewares(Route $route): self
    {
        $resolved = [];

        foreach ($route->getMiddlewareStack() as $middleware) {
            $resolved[] = $this->resolveMiddleware($middleware);
        }

        $route->clearMiddlewareStack();
        $route->middleware($resolved);

        return $this;
    }

    /**
     * Resolve a middleware implementation, optionally from a container
     *
     * @param MiddlewareInterface|string $middleware
     *
     * @return MiddlewareInterface
     */
    private function resolveMiddleware($middleware): MiddlewareInterface
    {
        if (is_string($middleware)) {
            $middleware = $this->container->get($middleware);
        }

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware;
        }

        throw new InvalidArgumentException(
            sprintf('Could not resolve middleware class: %s', $middleware)
        );
    }

    /**
     * Resolve handle
     *
     * @return self
     */
    private function resolveHandler(Route $route): self
    {
        $handler = $route->getHandler();

        if (is_string($handler) && strpos($handler, '::') !== false) {
            $handler = explode('::', $handler);
        } elseif (is_string($handler) && strpos($handler, '@') !== false) {
            $handler = explode('@', $handler);
        }

        if (
            is_array($handler)
            && count($handler) == 2
            && isset($handler[0])
            && is_object($handler[0])
        ) {
            $handler = [$handler[0], $handler[1]];
        }

        if (
            is_array($handler)
            && count($handler) == 2
            && isset($handler[0])
            && is_string($handler[0])
        ) {
            $handler = [$this->resolveClass($handler[0]), $handler[1]];
        }

        if (is_string($handler) && method_exists($handler, '__invoke')) {
            $handler = [$this->resolveClass($handler), '__invoke'];
        }

        if (!is_callable($handler)) {
            throw new InvalidArgumentException('Could not resolve a callable for this route');
        }

        $route->setHandler($handler);
        return $this;
    }

    /**
     * Get an object instance from a class name
     *
     * @param string                  $class
     *
     * @return object
     */
    private function resolveClass(string $class)
    {
        return $this->container->get($class);
    }
}
