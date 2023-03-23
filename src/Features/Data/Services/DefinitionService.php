<?php

namespace Kellton\Tools\Features\Data\Services;

use BackedEnum;
use Kellton\Tools\Features\Data\Data;
use Kellton\Tools\Features\Data\Definition;
use Kellton\Tools\Features\Data\Enums\BuildInType;
use Kellton\Tools\Features\Data\Exceptions\MissingConstructor;
use Illuminate\Support\Collection;
use Kellton\Tools\Features\Data\Property;
use LogicException;
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
     * @param string|BuildInType $class
     *
     * @return Definition
     *
     * @throws ReflectionException
     * @throws MissingConstructor
     */
    public function get(string|BuildInType $class): Definition
    {
        $name = $class;
        if ($class instanceof BuildInType) {
            $name = $class->name;
        }

        if (!$this->definitions->has($name)) {
            $content = $class instanceof BuildInType ? $class : new ReflectionClass($class);

            $this->definitions->put($name, $this->create($content));
        }

        return $this->definitions->get($name);
    }

    /**
     * Create a new instance based on the given reflection class.
     *
     * @param ReflectionClass|BuildInType $class
     *
     * @return Definition
     *
     * @throws MissingConstructor
     */
    public function create(ReflectionClass|BuildInType $class): Definition
    {
        if (is_subclass_of($class->name, Data::class)) {
            $constructor = collect($class->getMethods())->first(
                fn (ReflectionMethod $method) => $method->isConstructor()
            );
            if (!$constructor) {
                throw new MissingConstructor($class);
            }
        }

        $properties = $this->resolveProperties($class);

        return new Definition($class->name, $properties);
    }

    /**
     * Resolve properties.
     *
     * @param ReflectionClass|BuildInType $class
     *
     * @return Collection|Property
     */
    private function resolveProperties(ReflectionClass|BuildInType $class): Collection|Property
    {
        if (is_subclass_of($class->name, Data::class)) {
            return collect($class->getProperties(ReflectionProperty::IS_PUBLIC))
                ->reject(fn (ReflectionProperty $property) => $property->isStatic())
                ->values()
                ->mapWithKeys(
                    fn (ReflectionProperty $property) => [$property->name => $this->propertyService->create($property)]
                );
        }

        if (is_subclass_of($class->name, BackedEnum::class)) {
            return $this->propertyService->createFromEnum($class);
        }

        if ($class instanceof BuildInType) {
            return $this->propertyService->createFromBuildInType($class);
        }

        throw new LogicException('This should not happen.');
    }
}
