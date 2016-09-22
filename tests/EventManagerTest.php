<?php

use PHPUnit\Framework\TestCase;

interface invokableCallback
{
    public function __invoke(\Venta\Contracts\Event\Event $event);
}

/**
 * Class EventManagerTest
 */
class EventManagerTest extends TestCase
{
    public function setUp()
    {
        $this->container = new \Venta\Container\Container();
        $this->em = new \Venta\Event\EventManager($this->container);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function attachThrowsWrongNameException()
    {
        $em = new \Venta\Event\EventManager($this->container);
        $em->attach('invalid*1name', 'observer', function () {
        });
    }

    /**
     * @test
     */
    public function callbacksAreCalledDescPriority()
    {
        $em = new \Venta\Event\EventManager($this->container);
        $em->attach('event', '3', function ($event) {
            echo "3";
        }, 3);
        $em->attach('event', '2', function ($event) {
            echo "2";
        }, 4);
        $em->attach('event', '1', function ($event) {
            echo "1";
        }, 5);
        $em->attach('event', '6', function ($event) {
            echo "6";
        }, 1);
        $em->attach('event', '4', function ($event) {
            echo "4";
        }, 2);
        $em->attach('event', '5', function ($event) {
            echo "5";
        }, 2);
        $this->expectOutputString('123456');
        $em->trigger('event');
    }

    /**
     * @test
     */
    public function callbacksAreCalledOnEventTrigger()
    {
        $callback = Mockery::mock(invokableCallback::class);
        $callback->shouldReceive('__invoke')
                 ->once()
                 ->andReturn('Callback called');
        $this->em->attach('event', 'observer', $callback);

        $this->assertEquals('Callback called', $this->em->trigger('event'));

    }

    /**
     * @test
     */
    public function clearAllEventListeners()
    {
        $this->em->attach('event', 'observer', function () {
        });
        $this->em->attach('event', 'other.observer', function () {
        });
        $observers = $this->em->getObservers();
        $eventObservers = $observers['event'];

        /* 2 observers must exist */
        $this->assertCount(2, $eventObservers);
        $this->em->clearListeners('event');

        /* 0 observers must exist for "event" event */
        $observers = $this->em->getObservers();
        $eventObservers = $observers['event'];
        $this->assertCount(0, $eventObservers);

        /* 1 observer was re-added */
        $this->em->attach('event', 'other.observer', function () {
        });
        $observers = $this->em->getObservers();
        $eventObservers = $observers['event'];
        $this->assertCount(1, $eventObservers);
    }

    /**
     * @test
     */
    public function constructorAcceptsContainer()
    {
        $em = new \Venta\Event\EventManager($this->container);

        $this->assertInstanceOf(\Venta\Contracts\Event\EventManager::class, $em);
        $this->classHasAttribute('observers');
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function detachThrowsException()
    {
        $this->em->attach('event', 'observer', function () {
        });
        $this->em->detach('event', 'observer.name');
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function duplicateObserversAreNotAttached()
    {
        $this->em->attach('event.name', 'observer.name', function () {
        });
        $this->em->attach('event.name', 'observer.name', function () {
        });
    }

    /**
     * @test
     */
    public function exitsWhenPropagationStopped()
    {
        $callback = Mockery::mock(invokableCallback::class);
        $em = new \Venta\Event\EventManager($this->container);
        $em->attach('event', 'should.never.be.called', $callback, 1);
        $em->attach('event', 'stop.propagation', function (\Venta\Contracts\Event\Event $event) {
            $event->stopPropagation();
        }, 2);
        $callback->shouldReceive('__invoke')->never();
        $em->trigger('event');
    }

    /**
     * @test
     */
    public function negativePriorityAllowed()
    {
        $em = new \Venta\Event\EventManager($this->container);
        $em->attach('event', '3', function ($event) {
            echo "3";
        }, -5);
        $em->attach('event', '2', function ($event) {
            echo "2";
        }, -3);
        $em->attach('event', '1', function ($event) {
            echo "1";
        }, 2);
        $this->expectOutputString('123');
        $em->trigger('event');
    }

    /**
     * @test
     */
    public function observerCanBeAttached()
    {
        $this->em->attach('event.name', 'observer.name', function () {
        });
        $observers = $this->em->getObservers();
        $observer = $observers['event.name']['observer.name'];

        $this->assertArrayHasKey('event.name', $observers);
        $this->assertArrayHasKey('observer.name', $observers['event.name']);
        $this->assertEquals(0, $observer->getPriority());
        $this->assertEquals('observer.name', $observer->getName());
        $this->assertEquals(function () {
        }, $observer->getCallback());
    }

    /**
     * @test
     */
    public function observerCanBeDetached()
    {
        $this->em->attach('event', 'observer', function () {
        });
        $observers = $this->em->getObservers();
        $this->assertCount(1, $observers['event']);
        $this->em->detach('event', 'observer');
        $observers = $this->em->getObservers();
        $this->assertCount(0, $observers['event']);
        $this->em->attach('event', 'observer', function () {
        });
    }

    /**
     * @test
     */
    public function sameObserverMayListenMultipleEvents()
    {
        $callback = function () {
            echo "do nothing";
        };
        $this->em->attach('event.name', 'observer.name', $callback);
        $this->em->attach('other.event.name', 'observer.name', $callback, 5);
        $observers = $this->em->getObservers();

        $this->assertArrayHasKey('event.name', $observers);
        $this->assertArrayHasKey('other.event.name', $observers);
        $this->assertArrayHasKey('observer.name', $observers['event.name']);
        $this->assertArrayHasKey('observer.name', $observers['other.event.name']);
        $this->assertEquals($callback, $observers['event.name']['observer.name']->getCallback());
        $this->assertEquals($callback, $observers['other.event.name']['observer.name']->getCallback());
        $this->assertEquals(5, $observers['other.event.name']['observer.name']->getPriority());
    }

    /**
     * @test
     */
    public function throwsExceptionWhenTriggerNonRegisteredEvent()
    {
        $em = new \Venta\Event\EventManager($this->container);
        $this->isNull($em->trigger('event'));
    }
}