<?php

use PHPUnit\Framework\TestCase;
use Venta\Event\Event;

/**
 * Class EventTest
 */
class EventTest extends TestCase
{
    /**
     * @test
     */
    public function canGetData()
    {
        $event = new Event('event', ['foo' => 'bar', 'baz' => null]);

        $this->assertEquals(['foo' => 'bar', 'baz' => null], $event->getData());
        $this->assertEquals('bar', $event->getData('foo'));
        $this->assertEquals('baz1', $event->getData('one', 'baz1'));
        $this->assertNull($event->getData('baz'));
        $this->assertNull($event->getData('foo1'));
    }

    /**
     * @test
     */
    public function canGetName()
    {
        $event = new Event('event');

        $this->assertEquals('event', $event->getName());
    }

    /**
     * @test
     */
    public function canStopPropagation()
    {
        $event = new Event('event');
        $this->assertEquals(false, $event->isPropagationStopped());

        $event->stopPropagation();
        $this->assertEquals(true, $event->isPropagationStopped());
    }
}