<?php

namespace Kellton\Tools\Exceptions;
use Exception;

/**
 * Class NotImplemented handles the exception when the method is not implemented.
 */
class NotImplemented extends Exception
{
    /**
     * NotImplemented constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = 'Not implemented!')
    {
        parent::__construct($message);
    }
}
