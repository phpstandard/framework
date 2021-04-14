<?php

namespace Framework\Bootstrappers;

use Framework\Contracts\Core\BootstrapperInterface;
use Framework\Contracts\View\ViewEngineFactoryInterface;
use Framework\Contracts\View\ViewFinderInterface;
use Framework\View\Engines\PhpViewEngine;

class ViewBootstrapper implements BootstrapperInterface
{
    /** @var ViewEngineFactoryInterface $factory */
    private $factory;

    /** @var ViewFinderInterface $finder */
    private $finder;

    public function __construct(
        ViewEngineFactoryInterface $factory,
        ViewFinderInterface $finder
    ) {
        $this->factory = $factory;
        $this->finder = $finder;
    }

    /**
     * @inheritDoc
     */
    public function bootstrap()
    {
        $this->factory
            ->addEngine('php', PhpViewEngine::class)
            ->addEngine('html', PhpViewEngine::class);

        $this->finder
            ->addExtension('php')
            ->addExtension('html');
    }
}
