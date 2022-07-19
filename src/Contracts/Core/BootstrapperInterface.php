<?php

namespace Framework\Contracts\Core;

/** @package Framework\Contracts\Core */
interface BootstrapperInterface
{
    /**
     * Method must be invoked after registration of all ServiceProviderInterface
     * implementations.
     *
     * @return void
     */
    public function bootstrap(): void;
}
