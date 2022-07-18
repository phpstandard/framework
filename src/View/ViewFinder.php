<?php

namespace Framework\View;

use Framework\Contracts\View\ViewFinderInterface;
use InvalidArgumentException;

/** @package Framework\View */
class ViewFinder implements ViewFinderInterface
{
    /** @var array $namespaces Array of the namespaces */
    private array $namespaces = [];

    /** @var string[] $paths Array of the paths without namespace to look for views */
    private array $paths = [];

    /** @var array $views An aray of name => path of the already found views */
    private array $views = [];

    /** @var string[] $extensions Array of the extentions */
    private array $extensions = [];

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
        } elseif ($this->isNamespaced($name)) {
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
     * @return array<string,string>
     */
    private function parseNamespaceSegments(string $name): array
    {
        $segments = explode(self::NAMESPACE_DELIMITER, $name);

        if (count($segments) !== 2) {
            throw new InvalidArgumentException(
                'View ' . $name . ' has an invalid name'
            );
        }

        if (!isset($this->namespaces[$segments[0]])) {
            throw new InvalidArgumentException(
                'View ' . $name . ' has an undefined namespace'
            );
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
        $possibleFileNames = $this->getPossibleFileNames($name);

        foreach ($paths as $path) {
            // Check if the file exists without appending the extension
            $fullPath = $path . '/' . $name;
            if (file_exists($fullPath)) {
                return $fullPath;
            }

            foreach ($possibleFileNames as $fileName) {
                $fullPath = $path . '/' . $fileName;

                if (file_exists($fullPath)) {
                    return $fullPath;
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
     * @return string[]
     */
    private function getPossibleFileNames(string $name): array
    {
        return array_map(function ($ext) use ($name) {
            return $name . '.' . $ext;
        }, $this->extensions);
    }
}
