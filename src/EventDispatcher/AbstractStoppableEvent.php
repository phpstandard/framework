<?php

declare(strict_types=1);

namespace Framework\EventDispatcher;

use Psr\EventDispatcher\StoppableEventInterface;

class AbstractStoppableEvent implements StoppableEventInterface
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

    public function stopPropagation()
    {
        $this->isPropagationStopped = true;
    }
}
