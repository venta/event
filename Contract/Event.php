<?php declare(strict_types = 1);

namespace Abava\Event\Contract;

/**
 * Interface Event
 *
 * @package Abava\Event\Contract
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
    public function getParam($name);

    /**
     * Get parameters passed to the event
     *
     * @return array
     */
    public function getParams();

    /**
     * Has this event indicated event propagation should stop?
     *
     * @return bool
     */
    public function isPropagationStopped();

    /**
     * Set the event name
     *
     * @param  string $name
     * @return void
     */
    public function setName(string $name);

    /**
     * Set event parameters
     *
     * @param  array $params
     * @return void
     */
    public function setParams(array $params);

    /**
     * Indicate whether or not to stop propagating this event
     *
     * @return void
     */
    public function stopPropagation();
}