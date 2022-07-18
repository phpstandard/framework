<?php

namespace Framework\Support;

use Framework\Contracts\Support\MiddlewareResolverInterface;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Server\MiddlewareInterface;

/** @package Framework\Support */
class MiddlewareResolver implements MiddlewareResolverInterface
{
    /**
     * @param ContainerInterface $container 
     * @return void 
     */
    public function __construct(
        private ContainerInterface $container
    ) {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     * @throws InvalidArgumentException 
     */
    public function resolve(
        MiddlewareInterface|string $middleware
    ): MiddlewareInterface {
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
