<?php

namespace Framework\Contracts\Support;

use Psr\Http\Server\MiddlewareInterface;

interface MiddlewareResolverInterface
{
    /**
     * Resolve a middleware implementation, optionally from a container
     *
     * @param MiddlewareInterface|string $middleware
     *
     * @return MiddlewareInterface
     */
    public function resolve($middleware): MiddlewareInterface;
}
