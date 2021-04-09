<?php

namespace Framework\Contracts\Container;

interface BootableServiceProviderInterface extends ServiceProviderInterface
{
    /**
     * Method must be invoked on registration of a service provider implementing
     * this interface. Provides ability for eager loading of Service Providers.
     *
     * @return void
     */
    public function boot();
}
