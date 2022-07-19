<?php

namespace Framework\Contracts\Support;

use Psr\Http\Server\MiddlewareInterface;

/** @package Framework\Contracts\Support */
interface MiddlewareResolverInterface
{
    /**
     * Resolve a middleware implementation, optionally from a container
     *
     * @param MiddlewareInterface|string $middleware
     *
     * @return MiddlewareInterface
     */
    public function resolve(
        MiddlewareInterface|string $middleware
    ): MiddlewareInterface;
}
