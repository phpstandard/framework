<?php

namespace Framework\Contracts\View;

use InvalidArgumentException;

/** @package Framework\Contracts\View */
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
     * @param bool $prepend
     * @return ViewFinderInterface
     */
    public function addNamespace(
        string $namespace,
        string $path,
        bool $prepend = false
    ): ViewFinderInterface;

    /**
     * Add extension
     *
     * @param string $extension
     * @return ViewFinderInterface
     */
    public function addExtension(string $extension): ViewFinderInterface;

    /**
     * Add base path
     *
     * @param string $path
     * @return ViewFinderInterface
     */
    public function addPath(string $path): ViewFinderInterface;
}
