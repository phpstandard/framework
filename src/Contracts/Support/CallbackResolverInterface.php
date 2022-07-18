<?php

namespace Framework\Contracts\Support;

/** @package Framework\Contracts\Support */
interface CallbackResolverInterface
{
    /**
     * Resolves a callback string or array to the callable
     *
     * @param string|callable $handler
     * @return callable
     */
    public function resolve(string|callable $callback): callable;
}
