<?php

namespace Framework\View;

use Framework\Contracts\View\ViewEngineInterface;
use Framework\Contracts\View\ViewInterface;

class View implements ViewInterface
{
    /** @var ViewEngineInterface $engine */
    private $engine;

    /** @var string $path Absolute path to the view file */
    private $path;

    /** @var array|null $data */
    private $data;

    public function __construct(
        ViewEngineInterface $engine,
        string $path,
        ?array $data = null
    ) {
        $this->engine = $engine;
        $this->path = $path;
        $this->data = $data;
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
    public function __toString()
    {
        return $this->render();
    }
}
