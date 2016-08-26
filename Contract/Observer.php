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
}