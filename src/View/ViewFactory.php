<?php

namespace Framework\View;

use Framework\Contracts\View\ViewEngineFactoryInterface;
use Framework\Contracts\View\ViewFactoryInterface;
use Framework\Contracts\View\ViewFinderInterface;
use Framework\Contracts\View\ViewInterface;
use InvalidArgumentException;

class ViewFactory implements ViewFactoryInterface
{
    /** @var ViewFinderInterface $finder */
    private $finder;

    /** @var ViewEngineFactoryInterface $engineFactory */
    private $engineFactory;

    /** @var array Associative array of the shared data */
    private $shared = [];

    public function __construct(
        ViewFinderInterface $finder,
        ViewEngineFactoryInterface $engineFactory
    ) {
        $this->finder = $finder;
        $this->engineFactory = $engineFactory;
    }

    /**
     * @inheritDoc
     */
    public function create($names, ?array $data = null): ViewInterface
    {
        if (!is_array($names) && !is_string($names)) {
            throw new InvalidArgumentException(
                'Argument 1 passed to ' . __METHOD__
                    . ' must either string or array type. '
                    . gettype($names) . ' is given.'
            );
        }

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
    public function share(string $key, $value = null)
    {
        $this->shared[$key] = $value;
    }

    private function createView(string $name, ?array $data = null): ViewInterface
    {
        $path = $this->finder->find($name);
        $engine = $this->engineFactory->getEngine($path);

        return new View(
            $engine,
            $path,
            array_merge($this->shared, $data ?: [])
        );
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @return boolean
     */
    private function exists(string $name): bool
    {
        try {
            $path = $this->finder->find($name);
        } catch (InvalidArgumentException $th) {
            return false;
        }

        return true;
    }
}
