<?php

namespace Kellton\Tools\Features\Dependency\Traits;

use Illuminate\Support\Collection;
use Kellton\Tools\Features\Dependency\Attributes\Dependency;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;

/**
 * Trait UseDependency handles possibility to lazy load dependency using Dependency attribute.
 *
 * @see Dependency
 */
trait UseDependency
{
    /**
     * @var Collection
     */
    protected Collection $dependencies;

    /**
     * UseDependency constructor.
     */
    public function __construct()
    {
        $this->setupDependencies();

        if (is_callable('parent::__construct')) {
            parent::__construct();
        }
    }

    /**
     * Load dependency when called.
     *
     * @param string $name
     *
     * @return void
     *
     * @noinspection MagicMethodsValidityInspection
     */
    public function __get(string $name)
    {
        $this->loadDependency($name);

        return $this->{$name} ?? null;
    }

    /**
     * Apply dependencies to class fields.
     *
     * @return void
     */
    private function setupDependencies(): void
    {
        $this->dependencies = collect();
        $reflectionClass = new ReflectionClass($this);
        $properties = collect($reflectionClass->getProperties());

        while ($parent = $reflectionClass->getParentClass()) {
            $parentProperties = collect($parent->getProperties());
            $reflectionClass = $parent;
            if ($parentProperties->isEmpty()) {
                continue;
            }

            $properties = $properties->merge($parentProperties);
        }

        /** @var ReflectionProperty $property */
        foreach ($properties as $property) {
            /** @var ReflectionAttribute $attribute */
            $attribute = collect($property->getAttributes(Dependency::class))->first();
            if (!$attribute) {
                continue;
            }

            $this->dependencies->put($property->getName(), $property->getType()?->getName());
            unset($this->{$property->getName()});
        }
    }

    /**
     * Load dependency from service container.
     *
     * @param string $name
     *
     * @return void
     */
    private function loadDependency(string $name): void
    {
        if ($this->dependencies->has($name)) {
            $this->{$name} = app($this->dependencies->get($name));
        }
    }
}
