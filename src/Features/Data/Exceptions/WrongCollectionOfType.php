<?php

namespace Kellton\Tools\Features\Data\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

/**
 * Class WrongCollectionOfType handles exception when provided type for CollectionOf attribute is not supported.
 */
class WrongCollectionOfType extends Exception
{
    /**
     * NotFoundException constructor.
     *
     * @param string $message
     * @param int $code
     */
    #[Pure] public function __construct(string $message = 'Only Data and Enum classes are supported.', int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
