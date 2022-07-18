<?php

namespace Framework\Contracts\View;

/** @package Framework\Contracts\View */
interface ViewFactoryInterface
{
    /**
     * Create a view instance
     * 
     * If the $names is array, then the implementation should be in way to 
     * create a view instance first found view in the names array.
     *
     * @param string|string[] $names
     * @param array|null $data
     * @return ViewInterface
     */
    public function create(
        string|array $names,
        ?array $data = null
    ): ViewInterface;

    /**
     * Add shared data
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function share(string $key, mixed $value = null): void;
}
