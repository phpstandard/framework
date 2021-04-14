<?php

namespace Framework\Contracts\View;

interface ViewEngineInterface
{
    /**
     * Get the string content of the rendered view at path
     *
     * @param string $path
     * @param array|null $data
     * @return string
     */
    public function get(string $path, ?array $data = null): string;
}
