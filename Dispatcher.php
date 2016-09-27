<?php declare(strict_types = 1);

namespace Venta\Event;

use Venta\Contracts\Event\Dispatcher as DispatcherContract;
use Venta\Contracts\Event\ListenerResolver;

/**
 * Class Dispatcher
 *
 * @package Venta\Event
 */
class Dispatcher implements DispatcherContract
{
    /**
     * Array of wildcard listeners.
     *
     * @var array
     */
    protected $globalListeners = [];

    /**
     * Flag, defining if we are in dispatch process.
     *
     * @var bool
     */
    protected $isDispatching = false;

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
        }

        $this->listeners[$eventName][$priority][] = $listener;
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
        if (!$this->isDispatching) {
            $this->isDispatching = true;
            $listeners = $this->getListeners($eventName);
            $event = new Event($eventName, $data);

            foreach ($listeners as $index => $listener) {
                $listener($event);

                if ($event->isPropagationStopped()) {
                    $this->isDispatching = false;
                    break;
                }
            }

            $this->isDispatching = false;
        }
    }

    /**
     * Returns array of event listeners.
     *
     * @param  string $eventName
     * @return array
     */
    protected function getListeners(string $eventName): array
    {
        if (isset($this->listeners[$eventName])) {
            if (!isset($this->sortedListeners[$eventName])) {
                $resolver = $this instanceof ListenerResolver ? $this->getListenerResolver() : null;
                $mergedListeners = $this->listeners[$eventName] + $this->globalListeners;
                ksort($mergedListeners);

                foreach ($mergedListeners as $listeners) {
                    foreach ($listeners as $listener) {
                        if ($resolver !== null) {
                            $listener = $resolver($listener);
                        }

                        if (is_callable($listener)) {
                            $this->sortedListeners[$eventName][] = $listener;
                        }
                    }
                }
            }

            return $this->sortedListeners[$eventName];
        }

        return [];
    }
}