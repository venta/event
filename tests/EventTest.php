<?php

use PHPUnit\Framework\TestCase;

/**
 * Class ContainerTest
 */
class EventTest extends TestCase
{
    /**
     * @var \Venta\Event\Event
     */
    private $event;

    private $name = 'event';

    private $parameters = ['key' => 'value'];

    /**
     * @test
     */
    public function classCanBeInitiated()
    {
        $event = new \Venta\Event\Event($this->name, $this->parameters);
        $emptyEvent = new \Venta\Event\Event('empty.event');

        $this->assertInstanceOf(\Venta\Event\Contract\Event::class, $event);
        $this->assertInstanceOf(\Venta\Event\Contract\Event::class, $emptyEvent);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function constructorValidatesName()
    {
        $event = new \Venta\Event\Event('incorrect$name');
    }

    /**
     * @test
     */
    public function getAllParameters()
    {
        $event = new \Venta\Event\Event($this->name, $this->parameters);
        $this->assertEquals($this->parameters, $event->getParameters());
    }

    /**
     * @test
     */
    public function getSeparateParameter()
    {
        $event = new \Venta\Event\Event($this->name, $this->parameters);
        $this->assertEquals($this->parameters['key'], $event->getParameter('key'));
        $this->assertNull($event->getParameter('nonExisting'));
    }

    /**
     * @test
     */
    public function nameGetter()
    {
        $event = new \Venta\Event\Event('name');
        $this->assertEquals('name', $event->getName());
    }

    /**
     * @test
     */
    public function propagationCanBeStopped()
    {
        $event = new \Venta\Event\Event($this->name);
        $event->stopPropagation();
        $this->assertEquals(true, $event->isPropagationStopped());
    }

    /**
     * @test
     */

    public function propagationStoppedDisabledByDefault()
    {
        $event = new \Venta\Event\Event($this->name);
        $this->assertClassHasAttribute('propagationStopped', \Venta\Event\Event::class);
        $this->assertEquals(false, $event->isPropagationStopped());
    }
}