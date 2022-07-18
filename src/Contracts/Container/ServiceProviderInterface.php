<?php

namespace Framework\Contracts\Container;

use Framework\Contracts\Container\ContainerInterface;

/** @package Framework\Contracts\Container */
interface ServiceProviderInterface
{
    /**
     * Register services with the container
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function register(ContainerInterface $container): void;
}
