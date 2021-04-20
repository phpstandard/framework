<?php

declare(strict_types=1);

namespace Framework\Support;

use Framework\Contracts\Support\CallbackResolverInterface;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;

class CallbackResolver implements CallbackResolverInterface
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
    public function resolve($callback): callable
    {
        if (is_string($callback) && strpos($callback, '::') !== false) {
            $callback = explode('::', $callback);
        } elseif (is_string($callback) && strpos($callback, '@') !== false) {
            $callback = explode('@', $callback);
        }

        if (
            is_array($callback)
            && count($callback) == 2
            && is_object($callback[0])
        ) {
            $callback = [$callback[0], $callback[1]];
        }

        if (
            is_array($callback)
            && count($callback) == 2
            && is_string($callback[0])
        ) {
            $callback = [$this->resolveClass($callback[0]), $callback[1]];
        }

        if (is_string($callback) && method_exists($callback, '__invoke')) {
            $callback = [$this->resolveClass($callback), '__invoke'];
        }

        if (!is_callable($callback)) {
            throw new InvalidArgumentException('Could not resolve a callable');
        }

        return $callback;
    }

    /**
     * Get an object instance from a class name
     *
     * @param string                  $class
     *
     * @return object
     */
    private function resolveClass(string $class)
    {
        return $this->container->get($class);
    }
}
