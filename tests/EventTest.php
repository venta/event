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
        $event = $this->event;
        $emptyEvent = new \Abava\Event\Event('empty.event');

        $this->assertInstanceOf(\Abava\Event\Event::class, $event);
        $this->assertInstanceOf(\Abava\Event\Event::class, $emptyEvent);
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
        $this->assertEquals($this->parameters, $this->event->getParameters());
    }

    /**
     * @test
     */
    public function getSeparateParameter()
    {
        $this->assertEquals($this->parameters['key'], $this->event->getParameter('key'));
        $this->assertNull($this->event->getParameter('nonExisting'));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function nameIsValidatedInSetter()
    {
        $event = new \Abava\Event\Event('name');
        $event->setName('new*name');
    }

    /**
     * @test
     */
    public function nameSetterAndGetter()
    {
        $event = new \Abava\Event\Event('name');
        $newEventName = $event->setName('new.name');

        $this->assertEquals('name', $event->getName());
        $this->assertEquals('new.name', $newEventName->getName());
    }

    /**
     * @test
     */
    public function parametersCanBeSetOnExistingObject()
    {
        $eventWithNewParams = $this->event->setParameters(['other' => 'value']);
        $eventPreserveExistingParams = $this->event->setParameters(
            ['additional' => 'value'], true
        );

        $this->assertNotEquals($eventWithNewParams, $this->event);
        $this->assertArrayHasKey('additional', $eventPreserveExistingParams->getParameters());
        $this->assertArrayHasKey('key', $eventPreserveExistingParams->getParameters());
    }

    /**
     * @test
     */
    public function propagationCanBeStopped()
    {
        $event = $this->event;
        $event->stopPropagation();
        $this->assertEquals(true, $event->isPropagationStopped());
    }

    /**
     * @test
     */

    public function propagationStoppedDisabledByDefault()
    {
        $event = $this->event;
        $this->assertClassHasAttribute('propagationStopped', \Abava\Event\Event::class);
        $this->assertEquals(false, $event->isPropagationStopped());
    }

    public function setUp()
    {
        $this->event = new Abava\Event\Event($this->name, $this->parameters);
    }
}