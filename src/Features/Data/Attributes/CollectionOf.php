<?php

namespace Kellton\Tools\Features\Data\Attributes;

use BackedEnum;
use Kellton\Tools\Features\Data\Data;
use Attribute;
use Kellton\Tools\Features\Data\Exceptions\WrongCollectionOfType;

/**
 * Class CollectionOf handles the definition of collection of a property.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
readonly final class CollectionOf
{
    /**
     * CollectionOf constructor.
     *
     * @param class-string<Data> $class
     *
     * @throws WrongCollectionOfType
     */
    public function __construct(public string $class)
    {
        if (!is_subclass_of($this->class, Data::class) && !is_subclass_of($this->class, BackedEnum::class)) {
            throw new WrongCollectionOfType();
        }
    }
}
