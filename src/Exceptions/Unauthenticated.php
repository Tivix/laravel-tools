<?php

namespace Kellton\Tools\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Unauthorized handles unauthorized exceptions.
 */
class Unauthenticated extends Exception
{
    /**
     * Unauthenticated constructor.
     *
     * @param string $message
     * @param int $code
     */
    #[Pure] public function __construct(
        string $message = 'Authentication failed',
        int $code = Response::HTTP_UNAUTHORIZED
    ) {
        parent::__construct($message, $code);
    }
}
