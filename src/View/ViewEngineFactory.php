<?php

namespace Framework\View;

use Exception;
use Framework\Contracts\View\ViewEngineFactoryInterface;
use Framework\Contracts\View\ViewEngineInterface;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;

class ViewEngineFactory implements ViewEngineFactoryInterface
{
    /** @var ContainerInterface $container */
    private $container;

    /** @var array Associative array of the extentoin => ViewEngineInterface */
    private $engines = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function getEngine(string $path): ViewEngineInterface
    {
        foreach ($this->engines as $ext => $engine) {
            $ext = '.' . $ext;

            if (substr($path,  -strlen($ext)) === $ext) {
                if (is_string($engine)) {
                    $engine = $this->container->get($engine);
                }

                if ($engine instanceof ViewEngineInterface) {
                    return $engine;
                }

                throw new Exception(sprintf(
                    "Couldn't resolve view engine for the extension: %s",
                    $ext
                ));
            }
        }

        throw new InvalidArgumentException(
            'Unrecognized extension in file: ' . $path
        );
    }

    /**
     * @inheritDoc
     */
    public function addEngine(
        string $extention,
        $engine
    ): ViewEngineFactoryInterface {
        $this->engines[ltrim($extention, '.')] = $engine;
        return $this;
    }
}
