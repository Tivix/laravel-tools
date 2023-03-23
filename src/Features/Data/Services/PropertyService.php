<?php

namespace Kellton\Tools\Features\Data\Services;

use Kellton\Tools\Features\Data\Attributes\CollectionOf;
use Kellton\Tools\Features\Data\Attributes\CollectionOfIntegers;
use Kellton\Tools\Features\Data\Attributes\CollectionOfStrings;
use Kellton\Tools\Features\Data\Attributes\DefaultValue;
use Kellton\Tools\Features\Data\Attributes\MapName;
use Kellton\Tools\Features\Data\Data;
use Kellton\Tools\Features\Data\Enums\BuildInType;
use Kellton\Tools\Features\Data\Property;
use Illuminate\Support\Collection;
use Kellton\Tools\Undefined;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionProperty;
use TypeError;

/**
 * Class PropertyService handles the data reflection of a property.
 */
final class PropertyService
{
    /**
     * Returns a new instance based on the given reflection property.
     *
     * @param ReflectionProperty $property
     *
     * @return Property
     */
    public function create(ReflectionProperty $property): Property
    {
        $attributes = collect($property->getAttributes())->map(
            fn (ReflectionAttribute $reflectionAttribute) => $reflectionAttribute->newInstance()
        );

        /** @var MapName $mapName */
        $mapName = $attributes->first(fn ($attribute) => $attribute instanceof MapName);

        /** @var DefaultValue $defaultValue */
        $defaultValue = $attributes->first(fn ($attribute) => $attribute instanceof DefaultValue);

        $reflectionType = $property->getType();

        if ($reflectionType instanceof ReflectionNamedType) {
            $type = $reflectionType->getName();
            $isNullable = $reflectionType->allowsNull();
            $isUndefined = false;
            $isMixed = $type === 'mixed';
            $isDataObject = is_a($type, Data::class, true);
            $isCollection = is_a($type, Collection::class, true);
        } else {
            if ($reflectionType instanceof ReflectionIntersectionType) {
                throw new TypeError('Intersection types are not supported!');
            }

            $type = null;
            $isNullable = false;
            $isMixed = false;
            $isUndefined = false;
            $isDataObject = false;
            $isCollection = false;
            foreach ($reflectionType->getTypes() as $namedType) {
                if ($type === null && $namedType->getName() !== Undefined::class) {
                    $type = $namedType->getName();
                }

                $isNullable = $isNullable || $namedType->allowsNull();
                $isMixed = $namedType->getName() === 'mixed';
                $isUndefined = $isUndefined || is_a($namedType->getName(), Undefined::class, true);
                $isDataObject = $isDataObject || is_a($namedType->getName(), Data::class, true);
                $isCollection = $isCollection || is_a($namedType->getName(), Collection::class, true);
            }
        }

        $dataClass = $collectionType = null;

        if ($isDataObject) {
            $dataClass = $type;
        } elseif ($isCollection) {
            $collectionAttribute = $attributes->filter(function ($attribute) {
                return in_array(
                    $attribute::class,
                    [CollectionOf::class, CollectionOfIntegers::class, CollectionOfStrings::class],
                    true
                );
            });

            if ($collectionAttribute->count() > 1) {
                throw new TypeError(
                    'CollectionOf, CollectionOfIntegers and CollectionOfStrings are mutually exclusive!'
                );
            }

            /** @var CollectionOf $collectionOf */
            $collectionOf = $collectionAttribute->first();

            $collectionType = match ($collectionOf::class) {
                CollectionOf::class => $collectionOf->class,
                CollectionOfIntegers::class => BuildInType::int,
                CollectionOfStrings::class => BuildInType::string,
                default => throw new TypeError('Invalid collection type!'),
            };
        }

        return new Property(
            $property->name,
            $property->class,
            $type,
            $defaultValue instanceof DefaultValue ? $defaultValue->value : new Undefined(),
            $mapName?->name,
            $dataClass,
            $collectionType,
            $isNullable,
            $isMixed,
            $isUndefined,
            $isDataObject,
            $isCollection,
            $attributes,
        );
    }

    /**
     * Returns a new instance based on the given reflection enum.
     *
     * @param ReflectionClass $class
     *
     * @return Property
     */
    public function createFromEnum(ReflectionClass $class): Property
    {
        return new Property(
            $class->getShortName(),
            $class->getName(),
            $class->getName(),
            new Undefined(),
            null,
            null,
            null,
            false,
            false,
            false,
            false,
            false,
            collect(),
        );
    }

    public function createFromBuildInType(BuildInType $class)
    {
        return new Property(
            $class->name,
            $class->name,
            $class->name,
            new Undefined(),
            null,
            null,
            null,
            false,
            false,
            false,
            false,
            false,
            collect(),
        );
    }
}
