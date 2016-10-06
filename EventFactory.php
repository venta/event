<?php declare(strict_types = 1);

namespace Venta\Event;

use Venta\Contracts\Event\Event as EventContract;
use Venta\Contracts\Event\EventFactory as EventFactoryContract;

/**
 * Class EventFactory
 *
 * @package Venta\Event
 */
class EventFactory implements EventFactoryContract
{
    /**
     * @inheritDoc
     */
    public function create(string $eventName, array $data = []): EventContract
    {
        return new Event($eventName, $data);
    }
}