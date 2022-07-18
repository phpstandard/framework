<?php

namespace Framework\View;

use Framework\Contracts\View\ViewEngineFactoryInterface;
use Framework\Contracts\View\ViewFactoryInterface;
use Framework\Contracts\View\ViewFinderInterface;
use Framework\Contracts\View\ViewInterface;
use InvalidArgumentException;

/** @package Framework\View */
class ViewFactory implements ViewFactoryInterface
{
    /** @var array Associative array of the shared data */
    private array $shared = [];

    /**
     * @param ViewFinderInterface $finder
     * @param ViewEngineFactoryInterface $engineFactory
     * @return void
     */
    public function __construct(
        private ViewFinderInterface $finder,
        private ViewEngineFactoryInterface $engineFactory
    ) {
    }

    /**
     * @inheritDoc
     * @throws InvalidArgumentException
     */
    public function create(
        string|array $names,
        ?array $data = null
    ): ViewInterface {
        if (is_string($names)) {
            return $this->createView($names, $data);
        }

        if (count($names) == 1) {
            return $this->createView($names[0], $data);
        }

        foreach ($names as $name) {
            if (!$this->exists($name)) {
                continue;
            }

            return $this->createView($name, $data);
        }

        throw new InvalidArgumentException(
            'None of the views in the given array exist.'
        );
    }

    /**
     * @inheritDoc
     */
    public function share(string $key, mixed $value = null): void
    {
        $this->shared[$key] = $value;
    }

    /**
     * @param string $name
     * @param null|array $data
     * @return ViewInterface
     * @throws InvalidArgumentException
     */
    private function createView(
        string $name,
        ?array $data = null
    ): ViewInterface {
        $path = $this->finder->find($name);
        $engine = $this->engineFactory->getEngine($path);

        return new View(
            $engine,
            $path,
            array_merge($this->shared, $data ?: [])
        );
    }

    /**
     * @param string $name
     * @return bool
     */
    private function exists(string $name): bool
    {
        try {
            $this->finder->find($name);
        } catch (InvalidArgumentException) {
            return false;
        }

        return true;
    }
}
