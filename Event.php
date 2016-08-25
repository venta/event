<?php declare(strict_types = 1);

namespace Abava\Event;

use Abava\Event\Contract\Event as EventContract;

/**
 * Class Event
 *
 * @package Abava\Event
 */
class Event implements EventContract
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var bool
     */
    protected $propagationStopped = false;

    /**
     * Event constructor.
     *
     * @param string $name
     * @param array $params
     */
    public function __construct(string $name, array $parameters = [])
    {
        static::validateName($name);
        $this->name = $name;
        $this->parameters = $parameters;
    }

    /**
     * @param string $name
     * @return bool
     * @throws \InvalidArgumentException
     */
    public static function validateName(string $name)
    {
        if (!preg_match('/^[\w|\d|\.]*$/', $name)) {
            throw new \InvalidArgumentException('Event name must contain only [A-z], [0-9], "_", "."');
        }

        return true;
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
        static::validateName($name);
        $clone = clone $this;
        $clone->name = $name;

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getParameter(string $name)
    {
        if (!array_key_exists($name, $this->parameters)) {
            return null;
        }

        return $this->parameters[$name];
    }

    /**
     * @inheritdoc
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @inheritdoc
     */
    public function setParameters(array $parameters, bool $preserveExisting = false)
    {
        $clone = clone $this;
        if ($preserveExisting) {
            $clone->parameters = array_merge($this->parameters, $parameters);
        } else {
            $clone->parameters = $parameters;
        }

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }

    /**
     * @inheritdoc
     */
    public function stopPropagation()
    {
        $this->propagationStopped = true;
    }
}