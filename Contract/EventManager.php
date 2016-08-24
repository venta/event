<?php declare(strict_types = 1);

namespace Abava\Event\Contract;

use Abava\Container\Contract\Container;

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
    public function attach(string $eventName, string $observerName, $callback, int $priority = 0);

    /**
     * Clear all listeners for a given event
     *
     * @param  string $event
     * @return void
     */
    public function clearListeners(string $event);

    /**
     * Detaches a listener from an event
     *
     * @param string $eventName    the event name
     * @param string $observerName the observer name
     * @return bool true on success false on failure
     */
    public function detach(string $eventName, string $observerName);

    /**
     * Trigger an event
     *
     * @param  string $event
     * @param  array $argv
     * @return mixed
     */
    public function trigger(string $eventName, array $argv = []);
}