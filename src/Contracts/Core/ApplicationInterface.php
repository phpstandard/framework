<?php

namespace Framework\Contracts\Core;

use Framework\Contracts\Container\ContainerInterface;
use Framework\Contracts\Container\ServiceProviderInterface;

/** @package Framework\Contracts\Core */
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
     * @return ApplicationInterface 
     */
    public function setContainer(
        ContainerInterface $container
    ): ApplicationInterface;

    /**
     * Add service provider
     * 
     * @param ServiceProviderInterface|string $provider 
     * @return ApplicationInterface 
     */
    public function addServiceProvider(
        ServiceProviderInterface|string $provider
    ): ApplicationInterface;

    /**
     * Add bootstrapper
     * 
     * @param BootstrapperInterface|string $bootstrapper 
     * @return ApplicationInterface 
     */
    public function addBootstrapper(
        BootstrapperInterface|string $bootstrapper
    ): ApplicationInterface;

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
     * @return ApplicationInterface 
     */
    public function setBasePath(string $basePath): ApplicationInterface;

    /** 
     * Boot application
     *
     * @return void
     */
    public function boot(): void;
}
