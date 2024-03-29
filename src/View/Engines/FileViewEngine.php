<?php

namespace Framework\View\Engines;

use Framework\Contracts\View\ViewEngineInterface;

/** @package Framework\View\Engines */
class FileViewEngine implements ViewEngineInterface
{
    /**
     * @inheritDoc
     */
    public function get(string $path, ?array $data = null): string
    {
        return file_get_contents($path);
    }
}
