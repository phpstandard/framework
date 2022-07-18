<?php

declare(strict_types=1);

namespace Framework\Routing\Traits;

use Psr\Http\Server\MiddlewareInterface;

trait MiddlewareAwareTrait
{
    /**
     * @var array
     */
    protected array $middlewares = [];

    /**
     * Add middleware(s) to the stack
     *
     * @param MiddlewareInterface[]|MiddlewareInterface|string[]|string $middlewares
     * @param bool $prepend
     * @return static
     */
    public function middleware(
        MiddlewareInterface|string|array $middlewares,
        bool $prepend = false
    ): static {
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
     * Append middleware
     *
     * @param MiddlewareInterface|string $middleware
     * @return static
     */
    public function appendMiddleware(
        MiddlewareInterface|string $middleware
    ): static {
        $this->middlewares[] = $middleware;

        return $this;
    }

    /**
     *  Prepend middleware
     *
     * @param MiddlewareInterface|string $middleware
     * @return static
     */
    public function prependMiddleware(
        MiddlewareInterface|string $middleware
    ): static {
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
     * @return static
     */
    public function clearMiddlewareStack(): static
    {
        $this->middlewares = [];
        return $this;
    }
}
