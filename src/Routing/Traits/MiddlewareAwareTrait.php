<?php

declare(strict_types=1);

namespace Framework\Routing\Traits;

use Psr\Http\Server\MiddlewareInterface;

trait MiddlewareAwareTrait
{
    /**
     * @var array
     */
    protected $middlewares = [];

    /**
     * Add middleware(s) to the stack
     *
     * @param MiddlewareInterface[]|MiddlewareInterface|string[]|string $middlewares
     * @param bool $prepend
     * @return self
     */
    public function middleware($middlewares, $prepend = false): self
    {
        if (!is_array($middlewares)) {
            $middlewares = [$middlewares];
        }

        $middlewares = array_values($middlewares);

        if ($prepend) {
            $this->middlewares = array_merge($middlewares, $this->middlewares);
        } else {
            $this->middlewares = array_merge($this->middlewares, $middlewares);
        }

        return $this;
    }

    /**
     *  Append middleware
     *
     * @param MiddlewareInterface|string $middleware
     * @return self
     */
    public function appendMiddleware($middleware): self
    {
        $this->middlewares[] = $middleware;

        return $this;
    }

    /**
     *  Prepend middleware
     *
     * @param MiddlewareInterface|string $middleware
     * @return self
     */
    public function prependMiddleware($middleware): self
    {
        array_unshift($this->middlewares, $middleware);

        return $this;
    }

    /**
     * Get middleware stack
     *
     * @return array
     */
    public function getMiddlewareStack(): array
    {
        return $this->middlewares;
    }

    /**
     * Clear middleware stack
     *
     * @return self
     */
    public function clearMiddlewareStack(): self
    {
        $this->middlewares = [];
        return $this;
    }
}
