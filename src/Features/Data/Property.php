<?php

namespace Kellton\Tools\Features\Data;

use Illuminate\Support\Collection;

/**
 * Class Property handles the data reflection of a property.
 */
final class Property
{
    /**
     * Property constructor.
     *
     * @param string $name
     * @param string $className
     * @param string $type
     * @param mixed $defaultValue
     * @param string|null $mapName
     * @param string|null $dataClass
     * @param bool $isNullable
     * @param bool $isMixed
     * @param bool $isUndefined
     * @param bool $isDataObject
     * @param bool $isCollection
     * @param Collection $attributes
     */
    public function __construct(
        public readonly string $name,
        public readonly string $className,
        public readonly string $type,
        public readonly mixed $defaultValue,
        public readonly ?string $mapName,
        public readonly ?string $dataClass,
        public readonly bool $isNullable,
        public readonly bool $isMixed,
        public readonly bool $isUndefined,
        public readonly bool $isDataObject,
        public readonly bool $isCollection,
        public readonly Collection $attributes
    ) {
    }
}
