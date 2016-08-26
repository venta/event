<?php

use PHPUnit\Framework\TestCase;

/**
 * Class Test
 *
 * @package Abava\Event
 */
class ObserverTest extends TestCase
{
    /**
     * @test
     */
    public function constructorTest()
    {
        $observer = new \Abava\Event\Observer('name', function () {
        }, 0);

        $this->assertInstanceOf(\Abava\Event\Contract\Observer::class, $observer);
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
        $observer = new \Abava\Event\Observer('name%invalid', function () {
        }, 0);
    }
}
