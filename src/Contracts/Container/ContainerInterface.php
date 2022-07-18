<?php

namespace Framework\Contracts\Container;

use Psr\Container\ContainerInterface as PsrContainerInterface;

/** @package Framework\Contracts\Container */
interface ContainerInterface extends PsrContainerInterface
{
    /**
     * Set an entry
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
    ): ContainerInterface;

    /**
     * Call a method on the instance
     * 
     * @param string|object $instance Class name or instance of the class
     * @param string $method_name 
     * @return mixed 
     */
    public function callMehtod(
        string|object $instance,
        string $method_name
    ): mixed;
}
