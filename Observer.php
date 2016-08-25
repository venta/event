<?php declare(strict_types = 1);

namespace Abava\Event;

use Abava\Event\Contract\Observer as ObserverContract;

/**
 * Class Observer
 *
 * @package Abava\Event
 */
class Observer implements ObserverContract
{
    /**
     * @var string|callable $callback
     */
    protected $callback;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var integer $priority
     */
    protected $priority;

    /**
     * Observer constructor.
     *
     * @param string $name
     * @param string|callable $callback
     * @param integer $priority
     */
    public function __construct(string $name, $callback, $priority)
    {
        static::isNameValid($name);
        $this->setName($name);
        $this->setCallback($callback);
        $this->setPriority($priority);
    }

    /**
     * @param string $name
     * @return bool
     * @throws \InvalidArgumentException
     */
    public static function isNameValid(string $name)
    {
        if (!preg_match('/^[\w|\d|\.]*$/', $name)) {
            throw new \InvalidArgumentException('Observer name must contain only [A-z], [0-9], "_", "."');
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @inheritdoc
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setName(string $name)
    {
        static::isNameValid($name);
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @inheritdoc
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }
}