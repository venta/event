<?php declare(strict_types = 1);

namespace Venta\Event;

use Venta\Contracts\Event\Event as EventContract;
use Venta\Contracts\Event\EventDispatcher as EventDispatcherContract;
use Venta\Contracts\Event\EventSubscriber;

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
    public function subscribe(EventSubscriber $subscriber)
    {
        foreach ($subscriber->getSubscriptions() as $eventName => $subscriptions) {
            if (is_string($subscriptions)) {
                $subscriptions = [[$subscriptions]];
            }

            if (is_array($subscriptions) && is_string(reset($subscriptions))) {
                $subscriptions = [$subscriptions];
            }

            if (is_array($subscriptions)) {
                foreach ($subscriptions as $subscription) {
                    $priority = isset($subscription[1]) ? $subscription[1] : 0;
                    $this->attach($eventName, [$subscriber, $subscription[0]], $priority);
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function trigger(EventContract $event)
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