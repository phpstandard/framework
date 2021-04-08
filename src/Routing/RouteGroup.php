<?php

declare(strict_types=1);

namespace Framework\Routing;

class RouteGroup extends RouteCollector
{
    /**
     * Group prefix
     *
     * @var string
     */
    private $prefix;

    /**
     * Construct a route group
     *
     * @param string $prefix
     */
    public function __construct(string $prefix)
    {
        $this->setPrefix($prefix);
    }

    /**
     * Get group prefix
     *
     * @return  string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Set group prefix
     *
     * @param  string  $prefix  Group prefix
     *
     * @return  self
     */
    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
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
                $entity->setPath($this->getPrefix() . $entity->getPath());
                $map[] = $entity;
            } elseif ($entity instanceof RouteGroup) {
                $entity->setPrefix($this->getPrefix() . $entity->getPrefix());
                $map = array_merge($map, $entity->getRoutes());
            }
        }

        return $map;
    }
}
