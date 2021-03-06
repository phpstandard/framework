<?php

declare(strict_types=1);

namespace Framework\EventDispatcher;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcher implements EventDispatcherInterface
{
    /** @var ListenerProviderInterface $listenerProvider */
    private $listenerProvider;

    public function __construct(ListenerProviderInterface $listener_provider)
    {
        $this->listenerProvider = $listener_provider;
    }

    /**
     * @inheritDoc
     */
    public function dispatch(object $event)
    {
        $is_stoppable = $event instanceof StoppableEventInterface;

        if ($is_stoppable && $event->isPropagationStopped()) {
            return $event;
        }

        $listeners = $this->listenerProvider->getListenersForEvent($event);
        foreach ($listeners as $listener) {
            $listener($event);

            if ($is_stoppable && $event->isPropagationStopped()) {
                break;
            }
        }

        return $event;
    }
}
