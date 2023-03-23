<?php

namespace Kellton\Tools\Features\Data\Attributes;

use Attribute;

/**
 * Class CollectionOfIntegers handles the definition of collection containing only integers of a property.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
readonly final class CollectionOfIntegers
{
}
