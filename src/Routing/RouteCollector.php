<?php

declare(strict_types=1);

namespace Framework\Routing;

use Framework\Contracts\Routing\RouteInterface;
use Framework\Routing\Traits\MiddlewareAwareTrait;

/** @package Framework\Routing */
class RouteCollector
{
    use MiddlewareAwareTrait;

    /** @var (Route|RouteGroup)[] $collection */
    protected array $collection = [];

    /** @var null|Route[] $routes */
    private ?array $routes = null;

    /**
     * Create a new route
     *
     * @param string $method
     * @param string $path
     * @param callable|string $handle
     * @param null|string $name
     * @return Route
     */
    public static function route(
        string $method,
        string $path,
        callable|string $handle,
        ?string $name = null
    ): Route {
        $route = new Route($method, $path, $handle, $name);

        return $route;
    }

    /**
     * @param null|string $prefix
     * @param null|RouteGroup $parentGroup
     * @param null|string $name
     * @return RouteGroup
     */
    public static function group(
        ?string $prefix = null,
        ?RouteGroup $parentGroup = null,
        ?string $name = null
    ): RouteGroup {
        $group = new RouteGroup($prefix ?: '', $name);

        if ($parentGroup) {
            $parentGroup->addGroup($group);
        }

        return $group;
    }

    /**
     * Add route
     *
     * @param Route $route
     * @return RouteCollector
     */
    public function addRoute(Route $route): RouteCollector
    {
        $this->collection[] = $route;
        return $this;
    }

    /**
     * Add route group
     *
     * @param RouteGroup $group
     * @return RouteCollector
     */
    public function addGroup(RouteGroup $group): RouteCollector
    {
        $this->collection[] = $group;
        return $this;
    }

    /**
     * Create a new route
     *
     * @param string $method
     * @param string $path
     * @param callable|string $handle
     * @param null|string $name
     * @return RouteCollector
     */
    public function map(
        string $method,
        string $path,
        callable|string $handle,
        ?string $name = null
    ): RouteCollector {
        $route = new Route($method, $path, $handle, $name);
        $this->addRoute($route);

        return $this;
    }

    /**
     * Get route map
     *
     * @return Route[]
     */
    public function getRoutes(): array
    {
        if (is_null($this->routes)) {
            $this->routes = $this->generateRouteMap();
        }

        return $this->routes;
    }

    /**
     * @param string $name
     * @return RouteInterface|RouteGroup|null
     */
    public function getByName(string $name): RouteInterface|RouteGroup|null
    {
        foreach ($this->collection as $item) {
            if ($item->getName() == $name) {
                return $item;
            }

            if ($item instanceof RouteGroup) {
                $result = $item->getByName($name);
                if ($result) {
                    return $result;
                }
            }
        }

        return null;
    }

    /**
     * Generate route map
     *
     * @return Route[]
     */
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
}
