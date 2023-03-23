<?php

namespace Kellton\Tools\Features\Data\Attributes;

use Attribute;

/**
 * Class DefaultValue handles the default value of a property.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
readonly final class DefaultValue
{
    /**
     * DefaultValue constructor.
     *
     * @param mixed $value
     */
    public function __construct(public mixed $value)
    {
    }
}
