<?php declare(strict_types=1);

namespace Abava\Event\Contract;

interface Observer
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string|callable
     */
    public function getCallback();

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name);

    /**
     * @param callable|string $callback
     * @return void
     */
    public function setCallback($callback);

    /**
     * @param int $priority
     */
    public function setPriority(int $priority);
}