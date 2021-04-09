<?php

declare(strict_types=1);

namespace Framework\Container;

use Closure;
use Framework\Container\Exceptions\ContainerException;
use Framework\Container\Exceptions\NotFoundException;
use Framework\Contracts\Container\ContainerInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use Throwable;

class Container implements ContainerInterface
{
    /**
     * Registered services
     *
     * @var array
     */
    private $services = [];

    /**
     * Identifier for the registered singleton services
     *
     * @var array
     */
    private $singletons = [];

    /**
     * Resolved singletons
     *
     * @var array
     */
    private $resolved = [];

    /**
     * @inheritDoc
     */
    public function set(
        string $abstract,
        $concrete = null,
        bool $is_singleton = false
    ): ContainerInterface {
        if (isset($this->services[$abstract])) {
            throw new ContainerException("An entry with an id of {$abstract} is already registered");
        }

        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        $this->services[$abstract] = $concrete;

        if ($is_singleton && !isset($this->singletons[$abstract])) {
            $this->singletons[$abstract] = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        try {
            return $this->resolve($id);
        } catch (Throwable $th) {
            if (!$this->has($id)) {
                throw new NotFoundException($id);
            }

            throw $th;
        }
    }

    /**
     * @inheritDoc
     */
    public function has($id)
    {
        if (isset($this->services[$id])) {
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
     * Remove an entry from the container
     *
     * @param string $id
     * @return self
     */
    function unset($id): self
    {
        unset($this->services[$id]);
        unset($this->singletons[$id]);
        return $this;
    }

    /**
     * Resolve entry
     *
     * @param string $id
     * @return mixed
     */
    private function resolve(string $id)
    {
        $is_singleton = isset($this->singletons[$id]);
        if ($is_singleton && isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }

        $entry = $id;
        if (isset($this->services[$id])) {
            $entry = $this->services[$id];

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
            if (isset($this->services[$id])) {
                return $this->services[$id];
            }

            throw new ContainerException("{$id} is not resolvable", 0, $th);
        }

        $instance = $this->getInstance($reflector);

        if ($is_singleton) {
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
     * @return void
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

        $params = [];
        foreach ($constructor->getParameters() as $param) {
            if ($param->getType()) {
                $params[] = $this->resolveParameter($param);
            }
        }

        return $item->newInstanceArgs($params);
    }

    /**
     * Resolve constructor parameter
     *
     * @param ReflectionParameter $parameter
     * @return mixed a resolved parameter
     */
    private function resolveParameter(ReflectionParameter $parameter)
    {
        $type = $parameter->getType();

        if ($type !== null) {
            assert($type instanceof ReflectionNamedType);
            return $this->get($type->getName());
        } elseif ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new ContainerException("{$parameter->name} can't be instatiated and yet has no default value");
    }
}
