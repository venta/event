<?php

namespace spec\Venta\Event;

use PhpSpec\ObjectBehavior;
use stub\Venta\Event\StubEvent;
use stub\Venta\Event\StubEventListener;
use Venta\Contracts\Event\EventDispatcher as EventDispatcherContract;
use Venta\Event\EventDispatcher;

class EventDispatcherSpec extends ObjectBehavior
{
    function it_can_dispatch_event(StubEventListener $eventListener, StubEvent $event)
    {
//        $event = $event->getWrappedObject();
//        $this->addListener(StubEvent::class, [$eventListener, 'handle']);
//        $this->dispatch($event);
//        $eventListener->handle($event)->shouldHaveBeenCalled();
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(EventDispatcher::class);
        $this->shouldImplement(EventDispatcherContract::class);
    }
}
