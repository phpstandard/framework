<?php

namespace Framework\Core;

use Exception;
use Framework\Contracts\Container\ContainerInterface;
use Framework\Contracts\Container\ServiceProviderInterface;
use Framework\Contracts\Core\ApplicationInterface;
use Framework\Contracts\Core\BootstrapperInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

/** @package Framework\Core */
class Application implements ApplicationInterface
{
    /**
     * @param ContainerInterface $container 
     * @param (ServiceProviderInterface|string)[]|null $providers 
     * @param (BootstrapperInterface|string)[]|null $bootstrappers 
     * @param null|string $basePath Base (root) path of the app
     * @return void 
     */
    public function __construct(
        private ContainerInterface $container,
        private ?array $providers = null,
        private ?array $bootstrappers = null,
        private ?string $basePath = null
    ) {
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
    public function addServiceProvider(
        ServiceProviderInterface|string $provider
    ): ApplicationInterface {
        if (is_null($this->providers)) {
            $this->providers = [];
        }

        $this->providers[] = $provider;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addBootstrapper(
        BootstrapperInterface|string $bootstrapper
    ): ApplicationInterface {
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
    public function boot(): void
    {
        $this->invokeServiceProviders();
        $this->invokeBootstrappers();
    }

    /**
     * Invoke service providers
     * 
     * @return void 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     * @throws Exception 
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
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     * @throws Exception 
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
