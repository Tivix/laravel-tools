<?php

namespace Kellton\Tools\Feature\Data\Attributes;

use Kellton\Tools\Feature\Data\Data;
use Kellton\Tools\Feature\Data\Exceptions\CannotFindDataClass;
use Attribute;

/**
 * Class DataCollection handles the data collection of a property.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class DataCollection
{
    /**
     * DataCollection constructor.
     *
     * @param class-string<Data> $class
     *
     * @throws CannotFindDataClass
     */
    public function __construct(public string $class)
    {
        if (!is_subclass_of($this->class, Data::class)) {
            throw new CannotFindDataClass('Class given does not implement `DataObject::class`');
        }
    }
}