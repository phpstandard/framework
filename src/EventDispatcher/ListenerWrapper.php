<?php

declare(strict_types=1);

namespace Framework\EventDispatcher;

/** @package Framework\EventDispatcher */
class ListenerWrapper
{
    /**
     * Property type declarations support all type declarations supported by
     * PHP, with the exception of void and callable.
     *
     * @see https://wiki.php.net/rfc/typed_properties_v2
     * @see https://wiki.php.net/rfc/consistent_callables
     */

    /** @var string|callable $listener */
    private $listener;

    /**
     * @param string|callable $listener Listener callback
     * @param int $priority Listener priority
     * @param bool $isResolved Whether listener's callback is resolved or not
     * @return void
     */
    public function __construct(
        string|callable $listener,
        private int $priority,
        private bool $isResolved = false
    ) {
        $this->listener = $listener;
    }

    /**
     * Get listener priority
     *
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Set listener priority
     *
     * @param int $priority Listener priority
     * @return ListenerWrapper
     */
    public function setPriority(int $priority): ListenerWrapper
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Get listener callback
     *
     * @return string|callable
     */
    public function getListener(): string|callable
    {
        return $this->listener;
    }

    /**
     * Set listener callback
     *
     * @param string|callable  $listener Listener callback
     * @return ListenerWrapper
     */
    public function setListener(string|callable $listener): ListenerWrapper
    {
        $this->listener = $listener;
        return $this;
    }

    /**
     * Get whether listener's callback is resolved or not
     *
     * @return bool
     */
    public function getIsResolved(): bool
    {
        return $this->isResolved;
    }

    /**
     * Set whether listener's callback is resolved or not
     *
     * @param bool $isResolved Whether listener's callback is resolved or not
     * @return ListenerWrapper
     */
    public function setIsResolved(bool $isResolved): ListenerWrapper
    {
        $this->isResolved = $isResolved;
        return $this;
    }
}
