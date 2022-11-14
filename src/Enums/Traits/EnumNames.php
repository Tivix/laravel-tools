<?php

namespace Kellton\Tools\Enums\Traits;

use BackedEnum;
use Illuminate\Support\Collection;
use LogicException;
use UnitEnum;

/**
 * Trait EnumNames handles getting raw values or names from enum casts.
 * Should only be used inside enums.
 */
trait EnumNames
{
    /**
     * Get names from enum cases.
     *
     * @return Collection
     */
    public static function getNames(): Collection
    {
        return collect(static::cases())
            ->map(function (UnitEnum|BackedEnum $case) {
                return $case->name;
            });
    }

    /**
     * Convert to enum from provided name.
     * This will throw exception if enum name doesn't exist.
     *
     * @param string $name
     *
     * @return static
     */
    public static function fromName(string $name): static
    {
        $enum = static::tryFromName($name);

        if (!($enum instanceof static)) {
            throw new LogicException('Unsupported enum name!');
        }

        return $enum;
    }

    /**
     * Convert to enum from provided name.
     * This method will return null when enum name doesn't exist.
     *
     * @param string $name
     *
     * @return static|null
     */
    public static function tryFromName(string $name): ?static
    {
        return collect(static::cases())->first(function (UnitEnum|BackedEnum $case) use ($name) {
            return $case->name === $name;
        });
    }
}
