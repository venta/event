<?php declare(strict_types = 1);

namespace Venta\Event;

use Venta\Contracts\Event\Event as EventContract;
use Venta\Contracts\Event\EventDispatcher as EventDispatcherContract;
use Venta\Event\Exception\InvalidListenerException;

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
     * @inheritDoc
     * @throws InvalidListenerException
     */
    public function addListener(string $eventName, $listener, int $priority = 0)
    {
        if (!$this->canBeCalled($listener)) {
            throw new InvalidListenerException($listener);
        }

        $this->listeners[$eventName][$priority][] = $listener;
        unset($this->sortedListeners[$eventName]);
    }

    /**
     * @inheritDoc
     */
    public function dispatch(EventContract $event)
    {
        if (isset($this->dispatching[$this->getEventName($event)])) {
            return;
        }

        $this->dispatching[$this->getEventName($event)] = true;
        $listeners = $this->getListeners($this->getEventName($event));

        foreach ($listeners as $index => $listener) {
            $this->callListener($listener, $event);

            if ($event->isPropagationStopped()) {
                break;
            }
        }

        unset($this->dispatching[$this->getEventName($event)]);
    }

    /**
     * Performs call of the listener.
     *
     * @param mixed $listener
     * @param EventContract $event
     */
    protected function callListener($listener, EventContract $event)
    {
        $listener($event);
    }

    /**
     * Defines, if listener can be called.
     *
     * @param  mixed $listener
     * @return bool
     */
    protected function canBeCalled($listener): bool
    {
        return is_callable($listener);
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
            $this->sortedListeners[$eventName] = $this->sortEventListeners($eventName);
        }

        return $this->sortedListeners[$eventName];
    }

    /**
     * Merges all event listeners for particular event.
     *
     * @param  string $eventName
     * @return array
     */
    protected function sortEventListeners(string $eventName): array
    {
        $listeners = $this->listeners[$eventName];
        $normalised = [];

        ksort($listeners);

        foreach ($listeners as $priorityListeners) {
            foreach ($priorityListeners as $listener) {
                $normalised[] = $listener;
            }
        }

        return $normalised;
    }

    /**
     * Returns event name.
     *
     * @param  EventContract $event
     * @return string
     */
    private function getEventName(EventContract $event)
    {
        return ltrim(get_class($event), '\\');
    }
}