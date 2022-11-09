<?php

namespace Kellton\Tools\Feature\Data\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;
use Kellton\Tools\Feature\Data\Property;

/**
 * Class WrongDefaultValue handles the exception when a default value is not of the correct type.
 */
class WrongDefaultValue extends Exception
{
    /**
     * WrongDefaultValue constructor.
     */
    #[Pure] public function __construct(Property $property)
    {
        $message = 'Cannot assign a undefined value to a property "'
            . $property->name
            . '" that is non-undefined type. Set a default value or make the property undefined.';

        parent::__construct($message);
    }
}
