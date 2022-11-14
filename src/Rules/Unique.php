<?php

namespace Kellton\Tools\Rules;

use Illuminate\Contracts\Validation\Rule;
use Kellton\Tools\Builders\Builder;

/**
 * Class Unique handles rule for checking uniqueness.
 */
class Unique implements Rule
{
    /**
     * @var Builder
     */
    private Builder $builder;

    /**
     * Create a new rule instance.
     *
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function passes($attribute, $value): bool
    {
        return $this->builder->doesntExist();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('The :attribute has already been taken.');
    }
}
