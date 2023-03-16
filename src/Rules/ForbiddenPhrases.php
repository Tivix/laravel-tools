<?php

namespace Kellton\Tools\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

/**
 * Class ForbiddenPhrases handles rule for checking if the value contains forbidden phrases.
 */
readonly class ForbiddenPhrases implements Rule
{
    /**
     * Create a new rule instance.
     */
    public function __construct(private array $phrases)
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $value = Str::lower($value);
        foreach ($this->phrases as $phrase) {
            if (str_contains($value, $phrase)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Value contains forbidden phrases.';
    }
}
