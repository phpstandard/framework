<?php

declare(strict_types=1);

namespace Framework\Routing;

use Framework\Contracts\Routing\RouteInterface;
use Framework\Routing\Traits\MiddlewareAwareTrait;

class RouteCollector
{
    use MiddlewareAwareTrait;

    /** @var (Route|RouteGroup)[] $collection */
    protected $collection = [];

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
        $this->collection[] = $route;

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
        $this->collection[] = $group;

        return $this;
    }

    /**
     * Create a new route
     * 
     * @param string $method 
     * @param string $path 
     * @param mixed $handle 
     * @param null|string $name 
     * @return RouteCollector 
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
     * @param null|string $prefix 
     * @param null|RouteGroup $parent_group 
     * @param null|string $name 
     * @return RouteGroup 
     */
    public static function group(
        ?string $prefix = null,
        ?RouteGroup $parent_group = null,
        ?string $name = null
    ): RouteGroup {
        $group = new RouteGroup($prefix ?: '', $name);

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
        if (is_null($this->routes)) {
            $this->routes = $this->generateRouteMap();
        }

        return $this->routes;
    }

    /**
     * @param string $name 
     * @return RouteInterface|RouteGroup|null 
     */
    public function getByName(string $name)
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
