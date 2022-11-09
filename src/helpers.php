<?php

use Illuminate\Support\Collection;
use Kellton\Tools\Undefined;

if (!function_exists('is_undefined')) {
    /**
     * Check if the given variable is undefined.
     *
     * @param mixed $variable
     *
     * @return bool
     */
    function is_undefined(mixed $variable): bool
    {
        return $variable instanceof Undefined;
    }
}

if (!function_exists('collect_all')) {
    /**
     * Convert multidimensional arrays to collection.
     *
     * @param Collection|array $variable
     *
     * @return Collection
     */
    function collect_all(Collection|array $variable): Collection
    {
        if (!($variable instanceof Collection)) {
            $variable = collect($variable);
        }

        return $variable->recursive();
    }
}
