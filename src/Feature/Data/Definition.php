<?php

namespace Kellton\Tools\Feature\Data;

use Illuminate\Support\Collection;

/**
 * Class Definition handles the data reflection of a class.
 */
final class Definition
{
    /**
     * Definition constructor.
     *
     * @param class-string $class
     * @param Collection $properties
     */
    public function __construct(
        public readonly string $class,
        public readonly Collection $properties,
    ) {
    }
}
