<?php

namespace Framework\Bootstrappers;

use Framework\Contracts\Core\BootstrapperInterface;
use Framework\Contracts\View\ViewEngineFactoryInterface;
use Framework\Contracts\View\ViewFinderInterface;
use Framework\View\Engines\PhpViewEngine;

/** @package Framework\Bootstrappers */
class ViewBootstrapper implements BootstrapperInterface
{
    /**
     * @param ViewEngineFactoryInterface $factory
     * @param ViewFinderInterface $finder
     * @return void
     */
    public function __construct(
        private ViewEngineFactoryInterface $factory,
        private ViewFinderInterface $finder
    ) {
    }

    /**
     * @inheritDoc
     */
    public function bootstrap(): void
    {
        $this->factory
            ->addEngine('php', PhpViewEngine::class)
            ->addEngine('html', PhpViewEngine::class);

        $this->finder
            ->addExtension('php')
            ->addExtension('html');
    }
}
