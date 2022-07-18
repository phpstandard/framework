<?php

declare(strict_types=1);

namespace Framework\Routing;

/** @package Framework\Routing */
class RouteGroup extends RouteCollector
{
    /**
     * @param string $prefix
     * @param null|string $name
     * @return void
     */
    public function __construct(
        private string $prefix,
        private ?string $name = null
    ) {
        $this->setPrefix($prefix)
            ->setName($name);
    }

    /** @return string  */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     * @return RouteGroup
     */
    public function setPrefix(string $prefix): RouteGroup
    {
        $this->prefix = $prefix;
        return $this;
    }

    /** @return null|string  */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     * @return RouteGroup
     */
    public function setName(?string $name): RouteGroup
    {
        $this->name = $name;
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
