<?php

namespace Framework\View;

use Framework\Contracts\View\ViewFinderInterface;
use InvalidArgumentException;

class ViewFinder implements ViewFinderInterface
{
    /** @var array $namespaces Array of the namespaces */
    private $namespaces = [];

    /** @var string[] $paths Array of the paths without namespace to look for views */
    private $paths = [];

    /** @var array $views An aray of name => path of the already found views */
    private $views = [];

    /** @var string[] $extensions Array of the extentions */
    private $extensions = [];

    /**
     * @inheritDoc
     */
    public function find(string $name): string
    {
        if (isset($this->views[$name])) {
            return $this->views[$name];
        }

        if (file_exists($name) && is_file($name)) {
            $path = $name;
        } else if ($this->isNamespaced($name)) {
            $path = $this->findNamespacedView($name);
        } else {
            $path = $this->findInPaths($name, $this->paths);
        }

        $this->views[$name] = $path;
        return $path;
    }

    /**
     * @inheritDoc
     */
    public function addNamespace(
        string $namespace,
        string $path,
        bool $prepend = false
    ): ViewFinderInterface {
        if (!array_key_exists($namespace, $this->namespaces)) {
            $this->namespaces[$namespace] = [];
        }

        if ($prepend) {
            array_unshift($this->namespaces[$namespace], $path);
        } else {
            $this->namespaces[$namespace][] = $path;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addExtension(string $extension): ViewFinderInterface
    {
        $this->extensions[] = $extension;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addPath(string $path): ViewFinderInterface
    {
        $this->paths[] = $path;
        return $this;
    }

    /**
     * Check if the view name includes namespace
     *
     * @param string $name
     * @return boolean
     */
    private function isNamespaced(string $name): bool
    {
        return strpos(trim($name), self::NAMESPACE_DELIMITER) > 0;
    }

    /**
     * Find a view in the namespace
     *
     * @param string $name
     * @return string
     */
    private function findNamespacedView(string $name): string
    {
        [$namespace, $name] = $this->parseNamespaceSegments($name);
        return $this->findInPaths($name, $this->namespaces[$namespace]);
    }

    /**
     * Parse view name into namespace and the actual view name
     *
     * @param string $name
     * @return array
     */
    private function parseNamespaceSegments(string $name): array
    {
        $segments = explode(self::NAMESPACE_DELIMITER, $name);

        if (count($segments) !== 2) {
            throw new InvalidArgumentException('View ' . $name . ' has an invalid name');
        }

        if (!isset($this->namespaces[$segments[0]])) {
            throw new InvalidArgumentException('View ' . $name . ' has an undefined namespace');
        }

        return $segments;
    }

    /**
     * Find the first found view file
     *
     * @param string $name
     * @param array $paths
     * @return string
     */
    private function findInPaths(
        string $name,
        array $paths
    ): string {
        $possible_file_names = $this->getPossibleFileNames($name);

        foreach ($paths as $path) {
            // Check if the file exists without appending the extension
            $full_path = $path . '/' . $name;
            if (file_exists($full_path)) {
                return $full_path;
            }

            foreach ($possible_file_names as $file_name) {
                $full_path = $path . '/' . $file_name;

                if (file_exists($full_path)) {
                    return $full_path;
                }
            }
        }

        throw new InvalidArgumentException('View ' . $name . ' not found.');
    }

    /**
     * Get the array of the possible file names by appending 
     * the possible extensions to the name
     *
     * @param string $name
     * @return array
     */
    private function getPossibleFileNames(string $name): array
    {
        return array_map(function ($ext) use ($name) {
            return $name . '.' . $ext;
        }, $this->extensions);
    }
}
