<?php

namespace Framework\Contracts\Container;

use Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface
{
    /**
     * Set an entry
     *
     * @param string $abstract
     * @param mixed $concrete
     * @param boolean $shared
     * @return self
     */
    public function set(
        string $abstract,
        $concrete = null,
        bool $shared = false
    ): ContainerInterface;

    /**
     * Call a method on the instance
     *
     * @param string|object $instance Class name or instance of the class
     * @param string $method_name
     * @return mixed
     */
    public function callMehtod($instance, string $method_name);
}
