<?php

namespace Kellton\Tools\Features\Data\Attributes\Validation;

use Attribute;
use Illuminate\Contracts\Validation\Rule as ValidationRule;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Rule extends ValidationAttribute
{
    /**
     * Rule constructor.
     *
     * @param string|ValidationRule $rule
     * @param string|null $message
     */
    public function __construct(public readonly string|ValidationRule $rule, public readonly ?string $message = null)
    {
    }
}
