<?php

namespace Kellton\Tools\Features\Data\Services;

use Kellton\Tools\Features\Data\Definition;
use Kellton\Tools\Features\Data\Exceptions\MissingConstructor;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Class DefinitionService handles the data definitions.
 */
final class DefinitionService
{
    /**
     * @var Collection|null cached definitions
     */
    protected Collection|null $definitions = null;

    /**
     * DefinitionService constructor.
     *
     * @param PropertyService $propertyService
     */
    public function __construct(public readonly PropertyService $propertyService)
    {
        $this->definitions = collect();
    }

    /**
     * Returns the definition for a data class.
     *
     * @param string $class
     *
     * @return Definition
     *
     * @throws ReflectionException
     * @throws MissingConstructor
     */
    public function get(string $class): Definition
    {
        if (!$this->definitions->has($class)) {
            $this->definitions->put($class, $this->create(new ReflectionClass($class)));
        }

        return $this->definitions->get($class);
    }

    /**
     * Create a new instance based on the given reflection class.
     *
     * @param ReflectionClass $class
     *
     * @return Definition
     *
     * @throws MissingConstructor
     */
    public function create(ReflectionClass $class): Definition
    {
        $constructor = collect($class->getMethods())->first(fn (ReflectionMethod $method) => $method->isConstructor());
        if (!$constructor) {
            throw new MissingConstructor($class);
        }

        $properties = $this->resolveProperties($class);

        return new Definition($class->name, $properties);
    }

    /**
     * Resolve properties.
     *
     * @param ReflectionClass $class
     *
     * @return Collection
     */
    private function resolveProperties(ReflectionClass $class): Collection
    {
        return collect($class->getProperties(ReflectionProperty::IS_PUBLIC))
            ->reject(fn (ReflectionProperty $property) => $property->isStatic())
            ->values()
            ->mapWithKeys(
                fn (ReflectionProperty $property) => [$property->name => $this->propertyService->create($property)]
            );
    }
}
