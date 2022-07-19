<?php

namespace Framework\Providers;

use Framework\Contracts\Container\ContainerInterface;
use Framework\Contracts\Container\ServiceProviderInterface;
use Framework\Contracts\View\ViewEngineFactoryInterface;
use Framework\Contracts\View\ViewFactoryInterface;
use Framework\Contracts\View\ViewFinderInterface;
use Framework\View\ViewEngineFactory;
use Framework\View\ViewFactory;
use Framework\View\ViewFinder;

/** @package Framework\Providers */
class ViewServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(ContainerInterface $container): void
    {
        $container
            ->set(ViewFactoryInterface::class, ViewFactory::class, true)
            ->set(ViewEngineFactoryInterface::class, ViewEngineFactory::class, true)
            ->set(ViewFinderInterface::class, ViewFinder::class, true);
    }
}
