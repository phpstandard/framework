<?php

namespace Framework\Support;

use Framework\Contracts\Support\MiddlewareResolverInterface;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

class MiddlewareResolver implements MiddlewareResolverInterface
{
    /** @var ContainerInterface $container */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function resolve($middleware): MiddlewareInterface
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
}
