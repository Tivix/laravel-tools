<?php

namespace Kellton\Tools\Features\Dependency\Traits;

use Illuminate\Support\Collection;
use JetBrains\PhpStorm\Pure;
use Kellton\Tools\Features\Dependency\Attributes\Dependency;
use LogicException;
use ReflectionClass;

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
        $this->applyDependencies();

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
     *
     * @throws LogicException
     */
    public function __get(string $name)
    {
        if ($this->isDependencyExist($name)) {
            $this->{$name} = $this->loadDependency($name);

            return $this->{$name};
        }

        throw new LogicException('Undefined property "' . $name . '"');
    }

    /**
     * Apply dependencies to class fields.
     *
     * @return void
     */
    private function applyDependencies(): void
    {
        $this->dependencies = collect();
        $reflectionClass = new ReflectionClass($this);

        $attributes = collect($reflectionClass->getAttributes(Dependency::class));
        while ($parent = $reflectionClass->getParentClass()) {
            $parentAttributes = collect($parent->getAttributes(Dependency::class));
            $reflectionClass = $parent;
            if ($parentAttributes->isEmpty()) {
                continue;
            }

            $attributes = $attributes->merge($parentAttributes);
        }

        foreach ($attributes as $attribute) {
            /** @var Dependency $dependency */
            $dependency = $attribute->newInstance();
            $this->dependencies->put($dependency->getField(), $dependency->getClass());
        }
    }

    /**
     * Load dependency from service container.
     *
     * @param string $field
     *
     * @return object
     */
    private function loadDependency(string $field): object
    {
        return app($this->dependencies->get($field));
    }

    /**
     * Check if provided dependency was defined.
     *
     * @param string $name
     *
     * @return bool
     */
    #[Pure] private function isDependencyExist(string $name): bool
    {
        return $this->dependencies->has($name);
    }
}
