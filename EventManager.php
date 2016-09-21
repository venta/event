<?php declare(strict_types = 1);

namespace Venta\Event;

use Venta\Container\Contract\Container;
use Venta\Event\Contract\EventManager as EventManagerContract;
use Ds\Map;
use Ds\PriorityQueue;

/**
 * Class EventManager
 *
 * @package Venta\Event
 */
class EventManager implements EventManagerContract
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Map
     */
    protected $observers;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        $this->observers = new Map;
    }

    /**
     * @inheritdoc
     */
    public function attach(string $eventName, string $observerName, callable $callback, int $priority = 0)
    {
        Event::validateName($eventName);
        $observer = new Observer($observerName, $callback, $priority);
        $this->registerObserver($eventName, $observer);
    }

    /**
     * @inheritdoc
     */
    public function clearListeners(string $eventName)
    {
        $this->getEventObservers($eventName)->clear();
    }

    /**
     * @inheritdoc
     */
    public function detach(string $eventName, string $observerName)
    {
        $this->getObserver($eventName, $observerName);
        $this->getEventObservers($eventName)->remove($observerName);

        return !$this->getEventObservers($eventName)->hasKey($observerName);
    }

    /**
     * @return array[]
     */
    public function getObservers(): array
    {
        $events = [];
        foreach ($this->observers as $event => $observers) {
            $events[$event] = $observers->toArray();
        }

        return $events;
    }

    /**
     * @inheritdoc
     */
    public function trigger(string $eventName, array $argv = [])
    {
        Event::validateName($eventName);
        $observers = $this->enqueueObservers($eventName);
        if ($observers === null) {
            return;
        }
        $event = new Event($eventName, $argv);

        return $this->passToObservers($observers, $event);
    }

    /**
     * Call the observer callback
     *
     * @param Observer $observer
     * @param Event $event
     * @return mixed
     */
    protected function callObserver(Observer $observer, Event $event)
    {
        $callback = $observer->getCallback();
        //TODO: implement other call possibilities using container.
        //Just call a callback since we are typehinting it.
        return $callback($event);
    }

    /**
     * Enqueue all observers according to their priorities
     *
     * @param string $eventName
     * @return PriorityQueue|null
     * @throws \InvalidArgumentException
     */
    protected function enqueueObservers(string $eventName)
    {
        $observers = $this->getEventObservers($eventName);
        if ($observers->count() === 0) {
            return null;
        }
        $queue = new PriorityQueue;
        foreach ($observers as $observer) {
            $queue->push($observer, $observer->getPriority());
        }

        return $queue;
    }

    /**
     * Get observers attached to the event
     *
     * @param string $eventName
     * @return Map
     */
    protected function getEventObservers(string $eventName): Map
    {
        if (!$this->observers->hasKey($eventName)) {
            return new Map();
        }

        return $this->observers->get($eventName);
    }

    /**
     * Get observer fot the event if exists, else null
     *
     * @param string $eventName
     * @param string $observerName
     * @return mixed|null
     */
    protected function getObserver(string $eventName, string $observerName)
    {
        $eventObservers = $this->getEventObservers($eventName);
        if (($eventObservers->count() < 1) || !$eventObservers->hasKey($observerName)) {
            throw new \Exception(sprintf('Observer %s was not found in %s queue', $observerName, $eventName));
        }

        return $eventObservers->get($observerName);
    }

    /**
     * Send the Event to all subscribers according to priority
     *
     * @param PriorityQueue $observers
     * @param Event $event
     */
    protected function passToObservers(PriorityQueue $observers, Event $event)
    {
        $return = null;
        while ($observers->count() > 0) {
            $observer = $observers->pop();
            $return = $this->callObserver($observer, $event);
            if ($event->isPropagationStopped()) {
                return $return;
            }
        }

        return $return;
    }

    /**
     * Handle observer registration for the event
     *
     * @param string $eventName
     * @param Observer $observer
     * @throws \Exception
     */
    protected function registerObserver(string $eventName, Observer $observer)
    {
        $observers = $this->getEventObservers($eventName);
        if ($observers->hasKey($observer->getName())) {
            throw new \Exception(
                sprintf('Observer %s is already registered for %s event', $observer->getName(), $eventName));
        }
        $observers->put($observer->getName(), $observer);
        $this->observers->put($eventName, $observers);
    }
}