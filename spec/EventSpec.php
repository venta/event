<?php

namespace spec\Venta\Event;

use PhpSpec\ObjectBehavior;
use stub\Venta\Event\StubEvent;
use Venta\Event\AbstractEvent;

class EventSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf(StubEvent::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AbstractEvent::class);
    }

    function it_propagation_can_be_stopped()
    {
        $this->shouldNotBePropagationStopped();
        $this->stopPropagation();
        $this->shouldBePropagationStopped();
    }
}
