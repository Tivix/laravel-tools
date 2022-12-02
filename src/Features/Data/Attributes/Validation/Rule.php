<?php

namespace Kellton\Tools\Features\Data\Attributes\Validation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
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
