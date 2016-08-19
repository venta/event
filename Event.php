<?php declare(strict_types = 1);

namespace Abava\Event;

use Abava\Event\Contract\Event as EventContract;

class Event implements EventContract
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var bool
     */
    protected $propagationStopped = false;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @inheritdoc
     */
    public function getParam($name)
    {
        if (!isset($name, $this->params)) {
            return null;
        }

        return $this->params[$name];
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        static::validateName($name);
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function setParams(array $params)
    {
        $this->params = array_merge($this->params, $params);
    }

    /**
     * @inheritdoc
     */
    public function stopPropagation()
    {
        $this->propagationStopped = true;
    }

    /**
     * @inheritdoc
     */
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }

    /**
     * @param string $name
     * @return bool
     * @throws \InvalidArgumentException
     */
    public static function validateName($name)
    {
        if (!preg_match('/^[\w|\d|\.]*$/', $name)) {
            throw new \InvalidArgumentException('Event name must contain only [A-z], [0-9], "_", "."');
        }

        return true;
    }
}