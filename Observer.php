<?php declare(strict_types = 1);

namespace Venta\Event;

use Venta\Event\Contract\Observer as ObserverContract;

/**
 * Class Observer
 *
 * @package Venta\Event
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
    public function __construct(string $name, callable $callback, int $priority)
    {
        static::isNameValid($name);
        $this->name = $name;
        $this->callback = $callback;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return $this->priority;
    }
}