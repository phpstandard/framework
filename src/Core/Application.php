<?php

namespace Framework\Core;

use Exception;
use Framework\Contracts\Container\ContainerInterface;
use Framework\Contracts\Container\ServiceProviderInterface;
use Framework\Contracts\Core\ApplicationInterface;
use Framework\Contracts\Core\BootstrapperInterface;

class Application implements ApplicationInterface
{
    /** @var ContainerInterface $container */
    private $container;

    /** @var (ServiceProviderInterface|string)[]|null */
    private $providers;

    /** @var (BootstrapperInterface|string)[]|null */
    private $bootstrappers;

    /** @var string $basePath Base (root) path of the app */
    private $basePath;

    public function __construct(
        ContainerInterface $container,
        ?array $providers = null,
        ?array $bootstrappers = null,
        ?string $base_path = null
    ) {
        $this->container = $container;
        $this->providers = $providers;
        $this->bootstrappers = $bootstrappers;
        $this->basePath = $base_path;
    }

    /**
     * @inheritDoc
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * @inheritDoc
     */
    public function setContainer(
        ContainerInterface $container
    ): ApplicationInterface {
        $this->container = $container;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addServiceProvider($provider): ApplicationInterface
    {
        if (is_null($this->providers)) {
            $this->providers = [];
        }

        $this->providers[] = $provider;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addBootstrapper($bootstrapper): ApplicationInterface
    {
        if (is_null($this->bootstrappers)) {
            $this->bootstrappers = [];
        }

        $this->bootstrappers[] = $bootstrapper;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * @inheritDoc
     */
    public function setBasePath(string $basePath): ApplicationInterface
    {
        $this->basePath = $basePath;
        return $this;
    }

    /**
     * @inheritDoc
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
