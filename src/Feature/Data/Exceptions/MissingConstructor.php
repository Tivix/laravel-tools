<?php

namespace Kellton\Tools\Feature\Data\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;
use ReflectionClass;

/**
 * Class MissingConstructor handles the exception when a data class does not have a constructor.
 */
class MissingConstructor extends Exception
{
    /**
     * MissingConstructor constructor.
     *
     * @param ReflectionClass $class
     */
    #[Pure] public function __construct(ReflectionClass $class)
    {
        parent::__construct('The class ' . $class->name . ' does not have a constructor.');
    }
}
