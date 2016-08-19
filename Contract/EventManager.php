<?php declare(strict_types = 1);

namespace Abava\Event\Contract;

interface EventManager
{
    /**
     * Attaches a listener to an event
     *
     * @param string $eventName  the event to attach too
     * @param string $observerName
     * @param callable $callback a callable function
     * @param int $prioritythe   priority at which the $callback executed
     * @return bool true on success false on failure
     */
    public function attach($eventName, $observerName, $callback, $priority = 0);

    /**
     * Detaches a listener from an event
     *
     * @param string $eventName        the event name
     * @param string $observerName the observer name
     * @return bool true on success false on failure
     */
    public function detach($eventName, $observerName);

    /**
     * Clear all listeners for a given event
     *
     * @param  string $event
     * @return void
     */
    public function clearListeners($event);

    /**
     * Trigger an event
     *
     * @param  string $event
     * @param  array $argv
     * @return mixed
     */
    public function trigger($eventName, array $argv = []);
}