<?php

namespace Framework\Contracts\View;

use InvalidArgumentException;

interface ViewFinderInterface
{
    const NAMESPACE_DELIMITER = '::';

    /**
     * Find the fully qualified location of the view
     *
     * @param string $view
     * @return string
     * 
     * @throws InvalidArgumentException
     */
    public function find(string $view): string;

    /**
     * Add a new namespace
     * 
     * Should be implemented in a way to allow adding the same namespace with
     * different paths. 
     *
     * @param string $namespace
     * @param string $path
     * @param boolean $prepend
     * @return self
     */
    public function addNamespace(
        string $namespace,
        string $path,
        bool $prepend = false
    ): self;

    /**
     * Add extension
     *
     * @param string $extension
     * @return self
     */
    public function addExtension(string $extension): self;

    /**
     * Add base path
     *
     * @param string $path
     * @return self
     */
    public function addPath(string $path): self;
}
