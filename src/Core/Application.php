<?php

namespace Framework\Core;

use Exception;
use Framework\Contracts\Container\BootableServiceProviderInterface;
use Framework\Contracts\Container\ContainerInterface;
use Framework\Contracts\Container\ServiceProviderInterface;

class Application
{
    /** @var ContainerInterface $container */
    public $container;

    /** @var (ServiceProviderInterface|string)[]|null */
    private $providers;

    public function __construct(
        ContainerInterface $container,
        ?array $providers = null
    ) {
        $this->container = $container;
        $this->providers = $providers;
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
     * Boot application
     *
     * @return void
     */
    public function boot()
    {
        if (!$this->providers) {
            return;
        }

        /** @var ServiceProviderInterface[] $providers */
        $providers = [];

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
            $providers[] = $provider;
        }

        foreach ($providers as $provider) {
            if ($provider instanceof BootableServiceProviderInterface) {
                $this->container->callMehtod($provider, 'boot');
            }
        }
    }
}
