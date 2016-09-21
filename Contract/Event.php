<?php declare(strict_types = 1);

namespace Venta\Event\Contract;

/**
 * Interface Event
 *
 * @package Venta\Event\Contracts
 */
interface Event
{
    /**
     * Get event name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get a single parameter by name
     *
     * @param  string $name
     * @return array|null
     */
    public function getParameter(string $name);

    /**
     * Get parameters passed to the event
     *
     * @return array
     */
    public function getParameters();

    /**
     * Has this event indicated event propagation should stop?
     *
     * @return bool
     */
    public function isPropagationStopped();


    /**
     * Indicate whether or not to stop propagating this event
     *
     * @return void
     */
    public function stopPropagation();
}