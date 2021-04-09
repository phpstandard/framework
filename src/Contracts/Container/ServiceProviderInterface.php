<?php

namespace Framework\Contracts\Container;

use Framework\Contracts\Container\ContainerInterface;

interface ServiceProviderInterface
{
    /**
     * Register services with the container
     * 
     * @param ContainerInterface $container
     * @return void
     */
    public function register(ContainerInterface $container);
}
