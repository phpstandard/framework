<?php

namespace Framework\Contracts\Core;

use Framework\Contracts\Container\ContainerInterface;

interface ApplicationInterface
{
    /**
     * Get the value of container
     * 
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface;

    /**
     * Set the value of container
     *
     * @param ContainerInterface $container
     * @return self
     */
    public function setContainer(ContainerInterface $container): self;

    /**
     * Add service provider
     *
     * @param ServiceProviderInterface|string $provider
     * @return void
     */
    public function addServiceProvider($provider): self;

    /**
     * Add bootstrapper
     *
     * @param BootstrapperInterface|string $bootstrapper
     * @return void
     */
    public function addBootstrapper($bootstrapper): self;

    /**
     * Get the value of basePath
     *
     * @return string
     */
    public function getBasePath(): string;

    /**
     * Set the value of basePath
     *
     * @param string $basePath
     * @return self
     */
    public function setBasePath(string $basePath): self;

    /**
     * Boot application
     *
     * @return void
     */
    public function boot();
}
