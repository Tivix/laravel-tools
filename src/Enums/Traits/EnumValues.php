<?php

namespace Kellton\Tools\Enums\Traits;

use BackedEnum;
use Illuminate\Support\Collection;
use UnitEnum;

/**
 * Trait EnumValues handles getting raw values or names from enum casts.
 * Should only be used inside enums.
 */
trait EnumValues
{
    /**
     * Get values from all enum cases depends on enum type.
     *
     * @return Collection
     */
    public static function getValues(): Collection
    {
        return collect(static::cases())
            ->map(function (UnitEnum $case) {
                if (is_subclass_of($case, BackedEnum::class)) {
                    return $case->value;
                }

                return $case->name;
            });
    }
}
