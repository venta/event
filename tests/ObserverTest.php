<?php

use PHPUnit\Framework\TestCase;

/**
 * Class Test
 *
 * @package Venta\Event
 */
class ObserverTest extends TestCase
{
    /**
     * @test
     */
    public function constructorTest()
    {
        $observer = new \Venta\Event\Observer('name', function () {
        }, 0);

        $this->assertInstanceOf(\Venta\Event\Contract\Observer::class, $observer);
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
        $observer = new \Venta\Event\Observer('name%invalid', function () {
        }, 0);
    }
}
