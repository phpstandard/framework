<?php

namespace Framework\Contracts\View;

/** @package Framework\Contracts\View */
interface ViewEngineFactoryInterface
{
    /**
     * Find engine according the view file extension
     *
     * @param string $path Fully qualified path of the view
     * @return ViewEngineInterface
     */
    public function getEngine(string $path): ViewEngineInterface;

    /**
     * Add view engine
     *
     * @param string $extention
     * @param ViewEngineInterface|string $engine
     * @return ViewEngineFactoryInterface
     */
    public function addEngine(
        string $extention,
        ViewEngineInterface|string $engine
    ): ViewEngineFactoryInterface;
}
