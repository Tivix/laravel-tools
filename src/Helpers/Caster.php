<?php

namespace Kellton\Tools\Helpers;

use App\Types\Base\Undefined;
use BackedEnum;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use UnitEnum;

/**
 * Class Caster handles the casting data to other types.
 */
class Caster
{
    /**
     * Cast value to integer.
     *
     * @param mixed $value
     *
     * @return int|Undefined|null
     */
    public static function toInt(mixed $value): int|Undefined|null
    {
        if ($value === null) {
            return null;
        }

        if (is_undefined($value)) {
            return $value;
        }

        return (int) $value;
    }

    /**
     * Cast value to float.
     *
     * @param mixed $value
     *
     * @return float|Undefined|null
     */
    public static function toFloat(mixed $value): float|Undefined|null
    {
        if ($value === null) {
            return null;
        }

        if (is_undefined($value)) {
            return $value;
        }

        return (float) $value;
    }

    /**
     * Cast value to string.
     *
     * @param mixed $value
     *
     * @return string|Undefined|null
     */
    public static function toString(mixed $value): string|Undefined|null
    {
        if ($value === null) {
            return null;
        }

        if (is_undefined($value)) {
            return $value;
        }

        return (string) $value;
    }

    /**
     * Cast value to boolean.
     *
     * @param mixed $value
     *
     * @return bool|Undefined|null
     */
    public static function toBoolean(mixed $value): bool|Undefined|null
    {
        if ($value === null) {
            return null;
        }

        if (is_undefined($value)) {
            return $value;
        }

        return (bool) $value;
    }

    /**
     * Cast value to price.
     *
     * @param mixed $value
     *
     * @return float|Undefined|null
     */
    public static function toPrice(mixed $value): float|Undefined|null
    {
        if ($value === null) {
            return null;
        }

        if (is_undefined($value)) {
            return $value;
        }

        return (float) number_format((float) $value, 6);
    }

    /**
     * Cast value to date (Carbon object).
     *
     * @param mixed $value
     *
     * @return Carbon|Undefined|null
     */
    public static function toDate(mixed $value): Carbon|Undefined|null
    {
        if ($value === null) {
            return null;
        }

        if (is_undefined($value)) {
            return $value;
        }

        return new Carbon($value);
    }

    /**
     * Cast value to Collection (Carbon object).
     *
     * @param Collection|array|null $value
     *
     * @return Collection|Undefined|null
     */
    public static function toCollection(Collection|array|null $value): Collection|Undefined|null
    {
        if ($value === null) {
            return null;
        }

        if (is_undefined($value)) {
            return $value;
        }

        return collect_all($value);
    }

    /**
     * Cast value to enum object.
     *
     * @param string|Undefined|null $value
     * @param string $type
     *
     * @return UnitEnum|Undefined|null
     */
    public static function toEnum(string|Undefined|null $value, string $type): UnitEnum|Undefined|null
    {
        if ($value === null) {
            return null;
        }

        if (is_undefined($value)) {
            return $value;
        }

        if (is_subclass_of($type, BackedEnum::class)) {
            return $type::from($value);
        }

        if (is_subclass_of($type, UnitEnum::class)) {
            return collect($type::cases())->where('name', $value)->firstOrFail();
        }

        return null;
    }
}
