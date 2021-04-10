<?php

namespace Framework\Contracts\Core;

interface BootstrapperInterface
{
    /**
     * Method must be invoked after registration of all ServiceProviderInterface
     * implementations.
     *
     * @return void
     */
    public function bootstrap();
}
