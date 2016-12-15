<?php declare(strict_types = 1);

namespace Venta\Event;

use Venta\Contracts\Event\Event as EventContract;

/**
 * Class AbstractEvent
 *
 * @package Venta\Event
 */
abstract class AbstractEvent implements EventContract
{
    /**
     * Propagation stop flag.
     *
     * @var bool
     */
    private $propagationStop = false;

    /**
     * @inheritDoc
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStop;
    }

    /**
     * @inheritDoc
     */
    public function stopPropagation()
    {
        $this->propagationStop = true;
    }
}