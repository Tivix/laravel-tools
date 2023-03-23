<?php

namespace Kellton\Tools\Features\Data;

use Illuminate\Support\Collection;

/**
 * Class Definition handles the data reflection of a class.
 */
final readonly class Definition
{
    /**
     * Definition constructor.
     *
     * @param class-string $class
     * @param Collection|Property $properties
     */
    public function __construct(
        public string $class,
        public Collection|Property $properties,
    ) {
    }
}
