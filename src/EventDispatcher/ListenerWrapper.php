<?php

declare(strict_types=1);

namespace Framework\EventDispatcher;

class ListenerWrapper
{
    /**
     * Listener priority
     *
     * @var int
     */
    private $priority;

    /**
     * Listener callback
     *
     * @var string|callable
     */
    private $listener;

    /**
     * Whether listener's callback is resolved or not
     *
     * @var bool
     */
    private $isResolved;

    public function __construct(
        $listener,
        int $priority,
        bool $isResolved = false
    ) {
        $this->setListener($listener)
            ->setPriority($priority)
            ->setIsResolved($isResolved);
    }

    /**
     * Get listener priority
     *
     * @return  int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Set listener priority
     *
     * @param  int  $priority  Listener priority
     *
     * @return  self
     */
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get listener callback
     *
     * @return  string|callable
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * Set listener callback
     *
     * @param  string|callable  $listener  Listener callback
     *
     * @return  self
     */
    public function setListener($listener): self
    {
        $this->listener = $listener;

        return $this;
    }

    /**
     * Get whether listener's callback is resolved or not
     *
     * @return  bool
     */
    public function getIsResolved(): bool
    {
        return $this->isResolved;
    }

    /**
     * Set whether listener's callback is resolved or not
     *
     * @param  bool  $isResolved  Whether listener's callback is resolved or not
     *
     * @return  self
     */
    public function setIsResolved(bool $isResolved): self
    {
        $this->isResolved = $isResolved;

        return $this;
    }
}
