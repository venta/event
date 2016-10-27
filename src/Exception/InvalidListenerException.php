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
     * @param $listener
     * @param Throwable|null $previous
     */
    public function __construct($listener, Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Callable expected, got %s', is_object($listener) ? get_class($listener) : gettype($listener)),
            0,
            $previous
        );
    }

}