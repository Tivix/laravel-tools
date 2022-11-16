<?php

namespace Kellton\Tools\Features\Data\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

/**
 * Class CannotFindDataClass handles exception when data class is not found.
 */
class CannotFindDataClass extends Exception
{
    /**
     * NotFoundException constructor.
     *
     * @param string $message
     * @param int $code
     */
    #[Pure] public function __construct(string $message = 'Cannot find data class.', int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
