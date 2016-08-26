<?php

use PHPUnit\Framework\TestCase;

/**
 * Class ContainerTest
 */
class EventTest extends TestCase
{
    /**
     * @var \Abava\Event\Event
     */
    private $event;

    private $name = 'event';

    private $parameters = ['key' => 'value'];

    /**
     * @test
     */
    public function classCanBeInitiated()
    {
        $event = new \Abava\Event\Event($this->name, $this->parameters);
        $emptyEvent = new \Abava\Event\Event('empty.event');

        $this->assertInstanceOf(\Abava\Event\Contract\Event::class, $event);
        $this->assertInstanceOf(\Abava\Event\Contract\Event::class, $emptyEvent);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function constructorValidatesName()
    {
        $event = new \Abava\Event\Event('incorrect$name');
    }

    /**
     * @test
     */
    public function getAllParameters()
    {
        $event = new \Abava\Event\Event($this->name, $this->parameters);
        $this->assertEquals($this->parameters, $event->getParameters());
    }

    /**
     * @test
     */
    public function getSeparateParameter()
    {
        $event = new \Abava\Event\Event($this->name, $this->parameters);
        $this->assertEquals($this->parameters['key'], $event->getParameter('key'));
        $this->assertNull($event->getParameter('nonExisting'));
    }

    /**
     * @test
     */
    public function nameGetter()
    {
        $event = new \Abava\Event\Event('name');
        $this->assertEquals('name', $event->getName());
    }

    /**
     * @test
     */
    public function propagationCanBeStopped()
    {
        $event = new \Abava\Event\Event($this->name);
        $event->stopPropagation();
        $this->assertEquals(true, $event->isPropagationStopped());
    }

    /**
     * @test
     */

    public function propagationStoppedDisabledByDefault()
    {
        $event = new \Abava\Event\Event($this->name);
        $this->assertClassHasAttribute('propagationStopped', \Abava\Event\Event::class);
        $this->assertEquals(false, $event->isPropagationStopped());
    }
}