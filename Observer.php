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
     * @var string $name
     */
    protected $name;

    /**
     * @var string|callable $callback
     */
    protected $callback;

    /**
     * @var int $priority
     */
    protected $priority;

    /**
     * Observer constructor.
     *
     * @param string $name
     * @param string|callable $callback
     * @param int $priority
     */
    public function __construct(string $name, $callback, int $priority)
    {
        static::isNameValid($name);
        $this->name = $name;
        $this->callback = $callback;
        $this->priority = $priority;
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
    public function getCallback()
    {
        return $this->callback;
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
    public function setName(string $name)
    {
        static::isNameValid($name);
        $this->name = $name;
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
    public function setPriority(int $priority)
    {
        $this->priority = $priority;
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
}