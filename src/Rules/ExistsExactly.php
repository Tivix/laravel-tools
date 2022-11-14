<?php

namespace Kellton\Tools\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class ExistsExactly handles rule for checking that exactly expected number of records exists.
 */
class ExistsExactly implements Rule
{
    /**
     * @var Builder|Collection
     */
    private Builder|Collection $value;

    /**
     * Create a new rule instance.
     *
     * @param Builder|Collection $value
     */
    public function __construct(Builder|Collection $value)
    {
        $this->value = $value;
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
        return count($value) === $this->value->count();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('The :attribute contains elements that you do not have access to.');
    }
}
