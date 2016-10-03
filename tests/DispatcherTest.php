<?php

use PHPUnit\Framework\TestCase;
use Venta\Contracts\Event\ListenerResolver;
use Venta\Event\EventDispatcher;
use Venta\Event\Event;

/**
 * Class DispatcherTest
 */
class DispatcherTest extends TestCase
{
    /**
     * @test
     */
    public function canAttachAndClearListeners()
    {
        $dispatcher = $this->createDispatcher();
        $this->assertCount(0, $dispatcher->testGetListeners('event'));

        $dispatcher->attach('event', function () {});
        $dispatcher->attach('event', function () {});
        $this->assertCount(2, $dispatcher->testGetListeners('event'));

        $dispatcher->clearListeners('event');
        $this->assertCount(0, $dispatcher->testGetListeners('event', false));
    }

    /**
     * @test
     */
    public function canDefineWildcardListener()
    {
        $class = new stdClass;
        $dispatcher = $this->createDispatcher();

        $dispatcher->attach('*', function (Event $event) {
            $event->getData('class')->data = null;
        }, 5);

        $dispatcher->attach('event', function (Event $event) {
            $event->getData('class')->data = 'bar';
        }, 1);

        $class->data = 'foo';
        $dispatcher->trigger('event', ['class' => $class]);

        $this->assertNull($class->data);
    }

    /**
     * @test
     */
    public function firesListenersByPriority()
    {
        $dispatcher = $this->createDispatcher();
        $class = new stdClass;

        $dispatcher->attach('event', function (Event $event) {
            $event->getData('class')->number = 1;
        }, 2);

        $dispatcher->attach('event', function (Event $event) {
            $event->getData('class')->number = 2;
        }, 1);

        $dispatcher->trigger('event', ['class' => $class]);

        $this->assertEquals(1, $class->number);
    }

    /**
     * @test
     */
    public function canStopEventPropagation()
    {
        $dispatcher = $this->createDispatcher();
        $class = new stdClass;
        $class->foo = 'bar';

        $dispatcher->attach('event', function(Event $event) {
            $event->stopPropagation();
        });

        $dispatcher->attach('event', function(Event $event) {
            $event->getData('class')->foo = 'foo';
        });

        $dispatcher->trigger('event', ['class' => $class]);
        $this->assertEquals('bar', $class->foo);
    }

    /**
     * @test
     */
    public function ignoresCircularReference()
    {
        $dispatcher = $this->createDispatcher();
        $class = new stdClass;
        $class->number = '1';

        $dispatcher->attach('event', function(Event $event) use ($dispatcher) {
            $event->getData('class')->number .= '1';

            $dispatcher->trigger('event', ['class' => $event->getData('class')]);
        });

        $dispatcher->attach('event', function(Event $event) {
            $event->getData('class')->number .= '1';
        });

        $dispatcher->trigger('event', ['class' => $class]);
        $this->assertEquals('111', $class->number);

        $dispatcher->trigger('event', ['class' => $class]);
        $this->assertEquals('11111', $class->number);
    }

    /**
     * @test
     */
    public function wildcardListenersGoLast()
    {
        $dispatcher = $this->createDispatcher();
        $class = new stdClass;
        $class->number = 0;

        $dispatcher->attach('event', function(Event $event) {
            $event->getData('class')->number = 1;
        });

        $dispatcher->attach('*', function(Event $event) {
            $event->getData('class')->number = 2;
        });

        $dispatcher->attach('event', function(Event $event) {
            $event->getData('class')->number = 3;
        });

        $dispatcher->trigger('event', ['class' => $class]);
        $this->assertEquals(2, $class->number);
    }

    /**
     * Creates dispatcher instance.
     *
     * @return EventDispatcher|ListenerResolver|mixed
     */
    protected function createDispatcher()
    {
        return new class extends EventDispatcher
        {
            public function testGetListeners(string $eventName, $sorted = true)
            {
                if ($sorted === false) {
                    return isset($this->listeners[$eventName]) ? $this->listeners[$eventName] : [];
                }

                return $this->getListeners($eventName);
            }
        };
    }
}