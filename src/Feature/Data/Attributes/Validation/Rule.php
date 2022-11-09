<?php

namespace Kellton\Tools\Feature\Data\Attributes\Validation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Rule extends ValidationAttribute
{
    /**
     * Rule constructor.
     *
     * @param string $rule
     */
    public function __construct(public readonly string $rule)
    {
    }
}
