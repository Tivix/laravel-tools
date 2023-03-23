<?php

namespace Kellton\Tools\Features\Data\Attributes;

use Attribute;

/**
 * Class CollectionOfString handles the definition of collection containing only string of a property.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
readonly final class CollectionOfStrings
{
}
