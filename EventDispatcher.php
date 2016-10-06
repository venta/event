<?php declare(strict_types = 1);

namespace Venta\Event;

use Venta\Contracts\Event\Event as EventContract;
use Venta\Contracts\Event\EventDispatcher as EventDispatcherContract;
use Venta\Contracts\Event\EventFactory as EventFactoryContract;
use Venta\Event\EventFactory;

/**
 * Class EventDispatcher
 *
 * @package Venta\Event
 */
class EventDispatcher implements EventDispatcherContract
{
    /**
     * Array of events, being dispatched now.
     *
     * @var array
     */
    protected $dispatching = [];

    /**
     * Event factory holder.
     *
     * @var EventFactoryContract
     */
    protected $eventFactory;

    /**
     * Array of wildcard listeners.
     *
     * @var array
     */
    protected $globalListeners = [];

    /**
     * Array of defined events and it listeners.
     *
     * @var array
     */
    protected $listeners = [];

    /**
     * Sorted listeners array cache.
     *
     * @var array
     */
    protected $sortedListeners = [];

    /**
     * Construct function.
     *
     * @param EventFactoryContract|null $factory
     */
    public function __construct(EventFactoryContract $factory = null)
    {
        $this->eventFactory = $factory ?? new EventFactory;
    }

    /**
     * @inheritDoc
     */
    public function attach(string $eventName, callable $listener, int $priority = 0)
    {
        if ($eventName === '*') {
            $this->globalListeners[$priority][] = $listener;
        } else {
            $this->listeners[$eventName][$priority][] = $listener;
        }
    }

    /**
     * @inheritDoc
     */
    public function clearListeners(string $eventName)
    {
        if (isset($this->listeners[$eventName])) {
            unset($this->listeners[$eventName]);
        }

        if (isset($this->sortedListeners[$eventName])) {
            unset($this->sortedListeners[$eventName]);
        }
    }

    /**
     * @inheritDoc
     */
    public function trigger(string $eventName, array $data = [])
    {
        if (isset($this->dispatching[$eventName])) {
            return;
        }

        $this->dispatching[$eventName] = true;
        $listeners = $this->getListeners($eventName);
        $event = $this->eventFactory->create($eventName, $data);

        foreach ($listeners as $index => $listener) {
            $this->callListener($listener, $event);

            if ($event->isPropagationStopped()) {
                unset($this->dispatching[$eventName]);
                break;
            }
        }

        unset($this->dispatching[$eventName]);
    }

    /**
     * Performs call of the listener.
     *
     * @param callable      $listener
     * @param EventContract $event
     */
    protected function callListener(callable $listener, EventContract $event)
    {
        $listener($event);
    }

    /**
     * Returns array of event listeners.
     *
     * @param  string $eventName
     * @return array
     */
    protected function getListeners(string $eventName): array
    {
        if (!isset($this->listeners[$eventName])) {
            return [];
        }

        if (!isset($this->sortedListeners[$eventName])) {
            $this->sortedListeners[$eventName] = $this->mergeEventListeners($eventName);
        }

        return $this->sortedListeners[$eventName];
    }

    /**
     * Merges all event listeners for particular event.
     *
     * @param  string $eventName
     * @return array
     */
    protected function mergeEventListeners(string $eventName): array
    {
        $listeners = $this->listeners[$eventName];
        $globalListeners = $this->globalListeners;
        $normalised = [];

        ksort($listeners);
        ksort($globalListeners);

        foreach ([$listeners, $globalListeners] as $storage) {
            foreach ($storage as $listeners) {
                foreach ($listeners as $listener) {
                    $normalised[] = $listener;
                }
            }
        }

        return $normalised;
    }
}