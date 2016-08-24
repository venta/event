<?php declare(strict_types = 1);

namespace Abava\Event;

use Abava\Container\Contract\Container;
use Abava\Event\Contract\EventManager as EventManagerContract;
use Ds\PriorityQueue as Queue;
use Ds\Map;

/**
 * Class EventManager
 *
 * @package Abava\Event
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
    public function __construct(Container $container)
    {
        $this->observers = new Map;
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function attach(string $eventName, string $observerName, $callback, int $priority = 0)
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
        $this->getObservers($eventName)->clear();
    }

    /**
     * @inheritdoc
     */
    public function detach(string $eventName, string $observerName)
    {
        $observer = $this->getObserver($eventName, $observerName);
        if ($observer === null) {
            throw new \Exception(sprintf('Observer %s was not found in %s queue', $observerName, $eventName));
        }
        $this->getObservers($eventName)->remove($observerName);

        return !$this->getObservers($eventName)->hasKey($observerName);
    }

    /**
     * @inheritdoc
     */
    public function trigger(string $eventName, array $argv = [])
    {
        $observers = $this->enqueueObservers($eventName);
        $event = new Event($eventName, $argv);
        $this->passToSubscribers($observers, $event);
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
        if (is_callable($callback)) {
            return $callback($event);
        }
        if (is_string($callback)) {
            return $this->container->call($callback, ['event' => $event]);
        }
    }

    /**
     * Enqueue all observers according to their priorities
     *
     * @param string $eventName
     * @return Queue
     * @throws \InvalidArgumentException
     */
    protected function enqueueObservers(string $eventName): Queue
    {
        $observers = $this->getObservers($eventName);
        if ($observers->count() < 1) {
            throw new \InvalidArgumentException('Event name must be a registered event name');
        }
        $queue = new Queue;
        foreach ($observers as $observer) {
            $queue->push($observer, $observer->getPriority());
        }

        return $queue;
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
        $eventObservers = $this->getObservers($eventName);
        if (($eventObservers->count() < 1) || !$eventObservers->hasKey($observerName)) {
            return null;
        }

        return $eventObservers->get($observerName);
    }

    /**
     * Get observers attached to the event
     *
     * @param string $eventName
     * @return Map
     */
    protected function getObservers(string $eventName): Map
    {
        if (!$this->observers->hasKey($eventName)) {
            return new Map();
        }

        return $this->observers->get($eventName);
    }

    /**
     * Send the Event to all subscribers according to priority
     *
     * @param Map $observers
     * @param Event $event
     */
    protected function passToObservers(Queue $observers, Event $event)
    {
        while ($observers->count() > 0) {
            $observer = $observers->pop();
            $this->callObserver($observer, $event);
            if ($event->isPropagationStopped()) {
                break;
            }
        }
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
        $observers = $this->getObservers($eventName);
        if ($observers->hasKey($observer->getName())) {
            throw new \Exception(
                sprintf('Observer %s is already registered for %s event', $observers->getName(), $eventName));
        }
        $observers->put($observer->getName(), $observer);
        $this->observers->put($eventName, $observers);
    }

}