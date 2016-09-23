<?php

use PHPUnit\Framework\TestCase;

class EventObserverTest extends TestCase
{
    /**
     * @test
     */
    public function constructorTest()
    {
        $observer = new \Venta\Event\EventObserver('name', function () {
        }, 0);

        $this->assertInstanceOf(\Venta\Contracts\Event\EventObserver::class, $observer);
        $this->assertEquals('name', $observer->getName());
        $this->assertEquals(function () {
        }, $observer->getCallback());
        $this->assertEquals(0, $observer->getPriority());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function throwsExceptionOnInvalidName()
    {
        $observer = new \Venta\Event\EventObserver('name%invalid', function () {
        }, 0);
    }
}
