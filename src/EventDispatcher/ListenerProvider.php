<?php

declare(strict_types=1);

namespace Framework\EventDispatcher;

use Framework\Contracts\Support\CallbackResolverInterface;
use Framework\Support\CallbackResolver;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class ListenerProvider implements ListenerProviderInterface
{
    /**
     * Listener priority constants
     * Listeners with higher priority number will be called first.
     */
    public const PRIORITY_LOW = 0;
    public const PRIORITY_NORMAL = 50;
    public const PRIORITY_HIGH = 100;

    /** @var ContainerInterface $container */
    private $container;

    /** @var CallbackResolverInterface $resolver */
    private $resolver;

    /**
     * An associative array of the listener wrappers
     * Key is the type of the event, value is the array of the ListenerWrapper.
     *
     * @var array
     */
    private $wrappers = [];

    public function __construct(
        ContainerInterface $container,
        ?CallbackResolverInterface $resolver = null
    ) {
        $this->container = $container;
        $this->resolver = $resolver ?: new CallbackResolver($this->container);
    }

    /**
     * @inheritDoc
     */
    public function getListenersForEvent(object $event): iterable
    {
        yield from $this->getResolvedListeners($event);
    }

    /**
     * Add an event listener
     *
     * @param string $event_type
     * @param string|callable $listener
     * @param integer $priority
     * @return self
     */
    public function addEventListener(
        string $event_type,
        $listener,
        int $priority = self::PRIORITY_NORMAL
    ): self {
        if (!isset($this->wrappers[$event_type])) {
            $this->wrappers[$event_type] = [];
        }

        $this->wrappers[$event_type][] = new ListenerWrapper(
            $listener,
            $priority,
            false
        );

        return $this;
    }

    /**
     * @see addEventListener
     */
    public function on(
        string $event_type,
        $listener,
        int $priority = self::PRIORITY_NORMAL
    ): self {
        return $this->addEventListener($event_type, $listener, $priority);
    }

    /**
     * Resolve the listeners for event type and 
     * return resolved listeners iterable
     *
     * @param object $event 
     * @return iterable<callable>
     */
    private function getResolvedListeners(object $event): iterable
    {
        foreach ($this->getWrappers($event) as $wrapper) {
            $listener = $wrapper->getListener();

            if (!$wrapper->getIsResolved()) {
                $listener = $this->resolver->resolve($listener);
                $wrapper->setListener($listener)
                    ->setIsResolved(true);
            }

            yield $listener;
        }
    }

    /**
     * Get original unresolved listener handles match for the event type
     *
     * @param object $event
     * @return iterable<ListenerWrapper>
     */
    private function getWrappers(object $event): iterable
    {
        $all_wrappers = [];

        foreach ($this->wrappers as $event_type => $wrappers) {
            if (!$event instanceof $event_type) {
                continue;
            }

            $all_wrappers = array_merge($all_wrappers, $wrappers);
        }

        $this->sortWrappers($all_wrappers);
        yield from $all_wrappers;
    }

    /**
     * Sort listener wrappers by descending order priority
     *
     * @param ListenerWrapper[] $wrappers
     * @return void
     */
    private function sortWrappers(array &$wrappers): void
    {
        usort(
            $wrappers,
            function (ListenerWrapper $a, ListenerWrapper $b) {
                return $b->getPriority() <=> $a->getPriority();
            }
        );
    }
}
