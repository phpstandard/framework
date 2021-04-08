<?php

declare(strict_types=1);

namespace Framework\Routing;

use Framework\Routing\Traits\MiddlewareAwareTrait;

class RouteCollector
{
    use MiddlewareAwareTrait;

    /**
     * Array of the routes and groups
     *
     * @var array
     */
    private $collection = [];

    /** @var Route[] $route */
    private $routes;

    /**
     * Add route
     *
     * @param Route $route
     * @return self
     */
    public function addRoute(Route $route): self
    {
        $this->data[] = $route;

        return $this;
    }

    /**
     * Add route group
     *
     * @param RouteGroup $group
     * @return self
     */
    public function addGroup(RouteGroup $group): self
    {
        $this->data[] = $group;

        return $this;
    }

    /**
     * Create a new route
     *
     * @param string $method
     * @param string $path
     * @param callable $handle
     * @param string|null $name
     * @return Route
     */
    public function map(
        string $method,
        string $path,
        $handle,
        ?string $name = null
    ): self {
        $route = new Route($method, $path, $handle, $name);
        $this->addRoute($route);

        return $this;
    }

    /**
     * Create a new route
     *
     * @param string $method
     * @param string $path
     * @param callable $handle
     * @param string|null $name
     * @return Route
     */
    public static function route(
        string $method,
        string $path,
        $handle,
        ?string $name = null
    ): Route {
        $route = new Route($method, $path, $handle, $name);

        return $route;
    }

    /**
     * Create a new route group
     *
     * @param string|null $prefix
     * @param RouteGroup|null $parent_group
     * @return RouteGroup
     */
    public static function group(
        ?string $prefix = null,
        ?RouteGroup $parent_group = null
    ): RouteGroup {
        $group = new RouteGroup($prefix ?: '');

        if ($parent_group) {
            $parent_group->addGroup($group);
        }

        return $group;
    }

    /**
     * Get route map
     *
     * @return Route[]
     */
    public function getRoutes(): array
    {
        if (is_null($this->routeMap)) {
            $this->routeMap = $this->generateRouteMap();
        }

        return $this->routeMap;
    }

    protected function generateRouteMap(): array
    {
        $map = [];

        foreach ($this->collection as $entity) {
            $entity->middleware($this->getMiddlewareStack(), true);

            if ($entity instanceof Route) {
                $map[] = $entity;
            } elseif ($entity instanceof RouteGroup) {
                $map = array_merge($map, $entity->getRoutes());
            }
        }

        return $map;
    }

    /**
     * Resolve the middlewares
     *
     * @return self
     */
    // public function resolveMiddlewares(): self
    // {
    //     $resolved = [];

    //     foreach ($this->getMiddlewareStack() as $middleware) {
    //         $resolved[] = $this->resolveMiddleware($middleware);
    //     }

    //     $this->middleware = $resolved;
    //     return $this;
    // }

    /**
     * Resolve a middleware implementation, optionally from a container
     *
     * @param MiddlewareInterface|string $middleware
     * @param ContainerInterface|null    $container
     *
     * @return MiddlewareInterface
     */
    // protected function resolveMiddleware($middleware): MiddlewareInterface
    // {
    //     if (is_string($middleware) && class_exists($middleware)) {
    //         $middleware = new $middleware;
    //     }

    //     if ($middleware instanceof MiddlewareInterface) {
    //         return $middleware;
    //     }

    //     throw new InvalidArgumentException(sprintf('Could not resolve middleware class: %s', $middleware));
    // }
}