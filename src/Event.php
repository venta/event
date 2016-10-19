<?php declare(strict_types = 1);

namespace Venta\Event;

use Venta\Contracts\Event\Event as EventContract;

/**
 * Class Event
 *
 * @package Venta\Event
 */
abstract class Event implements EventContract
{
    /**
     * Propagation stop flag
     *
     * @var bool
     */
    protected $propagationStop = false;

    /**
     * @inheritDoc
     */
    public function stopPropagation()
    {
        $this->propagationStop = true;
    }

    /**
     * @inheritDoc
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStop;
    }
}