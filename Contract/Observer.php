<?php declare(strict_types=1);

namespace Abava\Event\Contract;

interface Observer
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string|callable
     */
    public function getCallback();

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @return void
     */
    public function setName($name);

    /**
     * @param callable|string $callback
     * @return void
     */
    public function setCallback($callback);

    /**
     * @param int $priority
     */
    public function setPriority($priority);
}