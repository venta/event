<?php declare(strict_types = 1);

namespace Abava\Event\Contract;

/**
 * Interface Observer
 *
 * @package Abava\Event\Contract
 */
interface Observer
{
    /**
     * @return string|callable
     */
    public function getCallback();

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return integer
     */
    public function getPriority();

    /**
     * @param callable|string $callback
     * @return void
     */
    public function setCallback($callback);

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name);

    /**
     * @param integer $priority
     */
    public function setPriority($priority);
}