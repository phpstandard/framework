<?php

declare(strict_types=1);

namespace Framework\EventDispatcher;

use Psr\EventDispatcher\StoppableEventInterface;

/** @package Framework\EventDispatcher */
abstract class AbstractStoppableEvent implements StoppableEventInterface
{
    /**
     * Propagation identicator of the event. 
     * True means propagation must be stoppped.
     *
     * @var boolean
     */
    private $isPropagationStopped = false;

    /**
     * @inheritDoc
     */
    public function isPropagationStopped(): bool
    {
        return $this->isPropagationStopped;
    }

    /** @return void  */
    public function stopPropagation(): void
    {
        $this->isPropagationStopped = true;
    }
}
