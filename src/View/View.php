<?php

namespace Framework\View;

use Framework\Contracts\View\ViewEngineInterface;
use Framework\Contracts\View\ViewInterface;
use Stringable;

class View implements ViewInterface, Stringable
{
    /**
     * @param ViewEngineInterface $engine
     * @param string $path Absolute path to the view file
     * @param null|array $data
     * @return void
     */
    public function __construct(
        private ViewEngineInterface $engine,
        private string $path,
        private ?array $data = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return $this->engine->get($this->path, $this->data);
    }

    /**
     * Get rendered string content of the view
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }
}
