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
     * @param boolean $is_singleton
     * @return self
     */
    public function set(
        string $abstract,
        $concrete = null,
        bool $is_singleton = false
    ): ContainerInterface;
}
