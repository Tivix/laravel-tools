<?php

namespace Kellton\Tools\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NotFound handles not found exceptions.
 */
class NotFound extends Exception
{
    /**
     * NotFoundException constructor.
     *
     * @param string $message
     * @param int $code
     */
    #[Pure] public function __construct(string $message = 'Resource not found', int $code = Response::HTTP_NOT_FOUND)
    {
        parent::__construct($message, $code);
    }
}
