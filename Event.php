<?php declare(strict_types = 1);

namespace Venta\Event;

use Venta\Contracts\Event\Event as EventContract;

/**
 * Class Event
 *
 * @package Venta\Event
 */
class Event implements EventContract
{
    /**
     * Array of event data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Event name.
     *
     * @var string
     */
    protected $name;

    /**
     * Propagation stop flag
     *
     * @var bool
     */
    private $propagationStop = false;

    /**
     * Construct function.
     *
     * @param string $name
     * @param array  $data
     */
    public function __construct(string $name, array $data = [])
    {
        $this->name = $name;
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function getData($key = null, $default = null)
    {
        return $key === null ? $this->data : (isset($this->data[$key]) ? $this->data[$key] : $default);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function stopPropagation()
    {
        $this->propagationStop = true;
    }

    /**
     * @inheritDoc
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStop;
    }
}