<?php

use PHPUnit\Framework\TestCase;
use Venta\Event\AbstractEvent;

/**
 * Class DispatcherTest
 */
class DispatcherTest extends TestCase
{
    /**
     * @test
     */
    public function canAttachAndDispatchAnEvent()
    {
        $event = $this->createEvent();
        $eventName = ltrim(get_class($event), '\\');
        $dispatcher = new \Venta\Event\EventDispatcher;

        $dispatcher->addListener($eventName, function (\Venta\Contracts\Event\Event $event) {
            $event->setData('foo', 'bar');
        });

        $dispatcher->addListener($eventName, function (\Venta\Contracts\Event\Event $event) {
            $event->setData('boo', 'far');
        });

        $dispatcher->dispatch($event);

        $this->assertInternalType('array', $event->getData());
        $this->assertContains('bar', $event->getData());
        $this->assertContains('far', $event->getData());
    }

    /**
     * @test
     */
    public function canStopPropagation()
    {
        $event = $this->createEvent();
        $eventName = ltrim(get_class($event), '\\');
        $dispatcher = new \Venta\Event\EventDispatcher;

        $dispatcher->addListener($eventName, function (\Venta\Contracts\Event\Event $event) {
            $event->setData('foo', 'bar');
        });

        $dispatcher->addListener($eventName, function (\Venta\Contracts\Event\Event $event) {
            $event->stopPropagation();
        });

        $dispatcher->addListener($eventName, function (\Venta\Contracts\Event\Event $event) {
            $event->setData('boo', 'far');
        });

        $dispatcher->dispatch($event);

        $this->assertTrue($event->isPropagationStopped());
        $this->assertNotContains('far', $event->getData());
    }

    /**
     * Returns fresh event instance
     *
     * @return \Venta\Contracts\Event\Event
     */
    protected function createEvent()
    {
        return new class extends AbstractEvent
        {
            protected $data;

            public function __construct()
            {
                $this->data = [];
            }

            public function getData()
            {
                return $this->data;
            }

            public function setData($key, $item)
            {
                $this->data[$key] = $item;
            }
        };
    }
}