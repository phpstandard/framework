<?php

declare(strict_types=1);

namespace Framework\Container;

use Closure;
use Framework\Container\Exceptions\ContainerException;
use Framework\Container\Exceptions\NotFoundException;
use Framework\Contracts\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use Throwable;

class Container implements ContainerInterface
{
    /** Registered definitions */
    private array $definitions = [];

    /** Identifier for the registered shared (singleton) services */
    private array $shared = [];

    /** Resolved shared (singleton) services */
    private array $resolved = [];

    /**
     * Set container definition
     * 
     * @param string $abstract 
     * @param mixed $concrete 
     * @param bool $shared 
     * @return ContainerInterface 
     */
    public function set(
        string $abstract,
        mixed $concrete = null,
        bool $shared = false
    ): ContainerInterface {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        $this->definitions[$abstract] = $concrete;

        if ($shared && !isset($this->shared[$abstract])) {
            $this->shared[$abstract] = true;
        }

        return $this;
    }

    /**
     * @param string|object $instance 
     * @param string $method_name 
     * @return mixed 
     * @throws ContainerException
     * @throws NotFoundException 
     * @throws Throwable 
     * @throws ReflectionException 
     */
    public function callMehtod(
        string|object $instance,
        string $method_name
    ): mixed {
        if (!is_object($instance) && !is_string($instance)) {
            throw new ContainerException(sprintf(
                'Argument 1 passed to %s must be type of either string or object. %s passed.',
                __METHOD__,
                gettype($instance)
            ));
        }

        if (is_string($instance)) {
            $instance = $this->get($instance);
        }

        $reflector = new ReflectionClass($instance);

        try {
            $method = $reflector->getMethod($method_name);
        } catch (Throwable $th) {
            throw new ContainerException(sprintf(
                "%s does not exists on",
                get_class($instance) . '::' . $method_name . '()'
            ), 0, $th);
        }

        $params = $this->getResolvedParameters($method);
        return $method->invokeArgs($instance, $params);
    }

    /**
     * @inheritDoc
     */
    public function get(string $id)
    {
        try {
            return $this->resolve($id);
        } catch (Throwable $th) {
            if (!$this->has($id)) {
                throw new NotFoundException($id, 0, $th);
            }

            throw $th;
        }
    }

    /**
     * @inheritDoc
     */
    public function has(string $id): bool
    {
        if (isset($this->definitions[$id])) {
            return true;
        }

        try {
            $this->getReflector($id);
            return true;
        } catch (Throwable $th) {
            return false;
        }

        return false;
    }

    /**
     * Resolve entry
     * 
     * @param string $id 
     * @return mixed 
     * @throws ContainerException
     * @throws ReflectionException 
     * @throws NotFoundException
     * @throws Throwable 
     */
    private function resolve(string $id)
    {
        $is_shared = isset($this->shared[$id]);
        if ($is_shared && isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }

        $entry = $id;
        if (isset($this->definitions[$id])) {
            $entry = $this->definitions[$id];

            if (is_object($entry)) {
                return $entry;
            }

            if ($entry instanceof Closure) {
                return $entry($this);
            }

            if (is_callable($entry)) {
                return $entry();
            }
        }

        try {
            $reflector = $this->getReflector($entry);
        } catch (Throwable $th) {
            if (isset($this->definitions[$id])) {
                return $this->definitions[$id];
            }

            throw new ContainerException("{$id} is not resolvable", 0, $th);
        }

        $instance = $this->getInstance($reflector);

        if ($is_shared) {
            $this->resolved[$id] = $instance;
        }

        return $instance;
    }

    /**
     * Get a ReflectionClass object representing the entry's class
     * 
     * @param string $entry
     * @return ReflectionClass
     */
    private function getReflector(string $entry): ReflectionClass
    {
        return new ReflectionClass($entry);
    }

    /**
     * Get an instance for the entry
     * 
     * @param ReflectionClass $item 
     * @return object|null 
     * @throws ContainerException 
     * @throws ReflectionException 
     * @throws NotFoundException 
     * @throws Throwable 
     */
    private function getInstance(ReflectionClass $item)
    {
        if (!$item->isInstantiable()) {
            throw new ContainerException("{$item->name} is not instantiable");
        }

        $constructor = $item->getConstructor();

        if (
            is_null($constructor)
            || $constructor->getNumberOfRequiredParameters() == 0
        ) {
            return $item->newInstance();
        }

        $params = $this->getResolvedParameters($constructor);
        return $item->newInstanceArgs($params);
    }

    /**
     * Get array of the resolved params
     * 
     * @param ReflectionMethod $method 
     * @return array 
     * @throws NotFoundException 
     * @throws Throwable 
     * @throws ReflectionException 
     * @throws ContainerException 
     */
    private function getResolvedParameters(ReflectionMethod $method): array
    {
        $params = [];
        foreach ($method->getParameters() as $param) {
            $params[] = $this->resolveParameter($param);
        }

        return $params;
    }

    /**
     * Resolve constructor parameter
     * 
     * @param ReflectionParameter $parameter 
     * @return mixed 
     * @throws NotFoundException 
     * @throws Throwable 
     * @throws ReflectionException 
     * @throws ContainerException 
     */
    private function resolveParameter(ReflectionParameter $parameter)
    {
        $type = $parameter->getType();

        if ($type !== null) {
            assert($type instanceof ReflectionNamedType);

            if (!$type->isBuiltin() && $this->has($type->getName())) {
                return $this->get($type->getName());
            }
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        if ($parameter->allowsNull()) {
            return null;
        }

        throw new ContainerException("{$parameter->name} can't be instatiated and yet has no default value");
    }
}
