<?php declare(strict_types = 1);

namespace Venta\Event\Contract;

/**
 * Interface Observer
 *
 * @package Venta\Event\Contracts
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