<?php

namespace Kellton\Tools\Features\Data\Attributes;

use Attribute;

/**
 * Class MapName handles mapping property name.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
readonly final class MapName
{
    /**
     * MapName constructor.
     *
     * @param string $name
     */
    public function __construct(public string $name)
    {
    }
}
