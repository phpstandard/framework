<?php

namespace Framework\Contracts\View;

/** @package Framework\Contracts\View */
interface ViewInterface
{
    /**
     * Get rendered string content of the view
     *
     * @return string
     */
    public function render(): string;
}
