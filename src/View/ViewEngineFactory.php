<?php

namespace Framework\View;

use Exception;
use Framework\Contracts\View\ViewEngineFactoryInterface;
use Framework\Contracts\View\ViewEngineInterface;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/** @package Framework\View */
class ViewEngineFactory implements ViewEngineFactoryInterface
{
    /** @var array Associative array of the extentoin => ViewEngineInterface */
    private array $engines = [];

    /**
     * @param ContainerInterface $container
     * @return void
     */
    public function __construct(private ContainerInterface $container)
    {
    }

    /**
     * @inheritDoc
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getEngine(string $path): ViewEngineInterface
    {
        foreach ($this->engines as $ext => $engine) {
            $ext = '.' . $ext;

            if (substr($path, -strlen($ext)) === $ext) {
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
        ViewEngineInterface|string $engine
    ): ViewEngineFactoryInterface {
        $this->engines[ltrim($extention, '.')] = $engine;
        return $this;
    }
}
