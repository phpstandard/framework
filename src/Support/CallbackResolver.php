<?php

declare(strict_types=1);

namespace Framework\Support;

use Framework\Contracts\Support\CallbackResolverInterface;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/** @package Framework\Support */
class CallbackResolver implements CallbackResolverInterface
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
     */
    public function resolve(string|callable $callback): callable
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
     * @param string $class 
     * @return mixed 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     */
    private function resolveClass(string $class): mixed
    {
        return $this->container->get($class);
    }
}
