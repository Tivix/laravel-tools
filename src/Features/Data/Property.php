<?php

namespace Kellton\Tools\Features\Data;

use Illuminate\Support\Collection;
use Kellton\Tools\Features\Data\Enums\BuildInType;

/**
 * Class Property handles the data reflection of a property.
 */
final readonly class Property
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
     * @param string|BuildInType|null $collectionType
     * @param bool $isNullable
     * @param bool $isMixed
     * @param bool $isUndefined
     * @param bool $isDataObject
     * @param bool $isCollection
     * @param Collection $attributes
     */
    public function __construct(
        public string $name,
        public string $className,
        public string $type,
        public mixed $defaultValue,
        public ?string $mapName,
        public ?string $dataClass,
        public string|BuildInType|null $collectionType,
        public bool $isNullable,
        public bool $isMixed,
        public bool $isUndefined,
        public bool $isDataObject,
        public bool $isCollection,
        public Collection $attributes
    ) {
    }
}
