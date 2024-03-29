<?php

namespace Framework\View\Engines;

use Framework\Contracts\View\ViewEngineInterface;

/** @package Framework\View\Engines */
class PhpViewEngine implements ViewEngineInterface
{
    /**
     * @inheritDoc
     */
    public function get(string $path, ?array $data = null): string
    {
        ob_start();

        $path_ctx = $path;
        $data_ctx = $data ?: [];

        (static function () use ($path_ctx, $data_ctx) {
            extract($data_ctx, EXTR_SKIP);
            require $path_ctx;
        })();

        return ob_get_clean();
    }
}
