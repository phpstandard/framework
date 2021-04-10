<?php

namespace Framework\Core;

use Exception;
use Framework\Contracts\Container\ContainerInterface;
use Framework\Contracts\Container\ServiceProviderInterface;
use Framework\Contracts\Core\BootstrapperInterface;

class Application
{
    /** @var ContainerInterface $container */
    public $container;

    /** @var (ServiceProviderInterface|string)[]|null */
    private $providers;

    /** @var (BootstrapperInterface|string)[]|null */
    private $bootstrappers;

    public function __construct(
        ContainerInterface $container,
        ?array $providers = null,
        ?array $bootstrappers = null
    ) {
        $this->container = $container;
        $this->providers = $providers;
        $this->bootstrappers = $bootstrappers;
    }

    /**
     * Add service provider
     *
     * @param ServiceProviderInterface|string $provider
     * @return void
     */
    public function addServiceProvider($provider)
    {
        if (is_null($this->providers)) {
            $this->providers = [];
        }

        $this->providers[] = $provider;
    }

    /**
     * Add bootstrapper
     *
     * @param BootstrapperInterface|string $bootstrapper
     * @return void
     */
    public function addBootstrapper($bootstrapper)
    {
        if (is_null($this->bootstrappers)) {
            $this->bootstrappers = [];
        }

        $this->bootstrappers[] = $bootstrapper;
    }

    /**
     * Boot application
     *
     * @return void
     */
    public function boot()
    {
        $this->invokeServiceProviders();
        $this->invokeBootstrappers();
    }

    /**
     * Invoke service providers
     *
     * @return void
     */
    private function invokeServiceProviders()
    {
        if (!$this->providers) {
            return;
        }

        foreach ($this->providers as $provider) {
            if (is_string($provider)) {
                $provider = $this->container->get($provider);
            }

            if (!($provider instanceof ServiceProviderInterface)) {
                throw new Exception(sprintf(
                    "%s must implement %s",
                    get_class($provider),
                    ServiceProviderInterface::class
                ));
            }

            $provider->register($this->container);
        }
    }

    /**
     * Invoke bootstrappers
     *
     * @return void
     */
    private function invokeBootstrappers()
    {
        if (!$this->bootstrappers) {
            return;
        }

        foreach ($this->bootstrappers as $bootstrapper) {
            if (is_string($bootstrapper)) {
                $bootstrapper = $this->container->get($bootstrapper);
            }

            if (!($bootstrapper instanceof BootstrapperInterface)) {
                throw new Exception(sprintf(
                    "%s must implement %s",
                    get_class($bootstrapper),
                    BootstrapperInterface::class
                ));
            }

            $bootstrapper->bootstrap();
        }
    }
}
