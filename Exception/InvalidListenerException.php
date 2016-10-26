<?php declare(strict_types = 1);

namespace Venta\Event\Exception;

use InvalidArgumentException;
use Throwable;

/**
 * Class InvalidListenerException
 *
 * @package Venta\Event\Exception
 */
class InvalidListenerException extends InvalidArgumentException
{
    /**
     * Construct function.
     *
     * @param                $listener
     * @param Throwable|null $previous
     */
    public function __construct($listener, Throwable $previous = null)
    {
        parent::__construct($this->createMessage($listener), 0, $previous);
    }

    /**
     * Returns error message.
     *
     * @param  mixed $listener
     * @return string
     */
    protected function createMessage($listener)
    {
        return sprintf('Invalid listener type. Callable expected, got %s', $listener);
    }
}