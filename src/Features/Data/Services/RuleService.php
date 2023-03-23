<?php

namespace Kellton\Tools\Features\Data\Services;

use BackedEnum;
use Carbon\Carbon;
use Kellton\Tools\Features\Data\Attributes\Validation\Rule;
use Kellton\Tools\Features\Data\Attributes\Validation\ValidationAttribute;
use Kellton\Tools\Features\Data\Definition;
use Kellton\Tools\Features\Data\Exceptions\MissingConstructor;
use Kellton\Tools\Features\Data\Property;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Enum;
use ReflectionException;
use TypeError;

/**
 * Class RuleService handles the rules of the data class.
 */
final class RuleService
{
    /**
     * RuleService constructor.
     *
     * @param DefinitionService $definitionService
     */
    public function __construct(public readonly DefinitionService $definitionService)
    {
    }

    /**
     * Returns rules for a data class by its definition.
     *
     * @param Definition $definition
     *
     * @return Collection
     *
     * @throws MissingConstructor
     * @throws ReflectionException
     */
    public function get(Definition $definition): Collection
    {
        $data = $definition->properties->mapWithKeys(fn (Property $property) => $this->resolve($property)->all());

        return $data;
    }

    /**
     * Returns rules for a data class by its class name.
     *
     * @param string $class
     *
     * @return Collection
     *
     * @throws MissingConstructor
     * @throws ReflectionException
     */
    public function getByClass(string $class): Collection
    {
        $definition = $this->definitionService->get($class);

        return $this->get($definition);
    }

    /**
     * Resolve the rules for a property.
     *
     * @param Property $property
     *
     * @return Collection
     *
     * @throws MissingConstructor
     * @throws ReflectionException
     */
    private function resolve(Property $property): Collection
    {
        $propertyName = $property->mapName ?? $property->name;

        if ($property->isDataObject || $property->isCollection) {
            return $this->getNestedRules($property, $propertyName);
        }

        return collect([$propertyName => $this->getRulesForProperty($property)]);
    }

    /**
     * Returns the rules for a property.
     *
     * @param Property $property
     *
     * @return Collection
     */
    protected function getRulesForProperty(Property $property): Collection
    {
        $data = collect([
            'rules' => collect(),
            'messages' => collect(),
        ]);

        /** @var Collection $rules */
        $rules = $data->get('rules');

        if ($property->isNullable) {
            $rules->add('nullable');
        }

        if ($property->isUndefined) {
            $rules->add('sometimes');
        }

        if (!$property->isNullable && !$property->isUndefined) {
            if ($property->isCollection) {
                $rules->add('array');
            } else {
                $rules->add('required');
            }
        }

        $this->resolveTypes($property, $rules);
        $this->resolveAttributeRules($property, $data);

        return $data;
    }

    /**
     * Returns the rules for a nested property.
     *
     * @param Property $property
     * @param string $propertyName
     *
     * @return Collection
     *
     * @throws MissingConstructor
     * @throws ReflectionException
     */
    protected function getNestedRules(Property $property, string $propertyName): Collection
    {
        $parentRules = $this->getRulesForProperty($property);

        $definition = match (true) {
            $property->isDataObject => $this->definitionService->get($property->dataClass),
            $property->isCollection => $this->definitionService->get($property->collectionType),
        };

        if ($property->isDataObject) {
            $prefix = "{$propertyName}.";
        } elseif ($property->isCollection) {
            if ($definition->properties instanceof Collection) {
                $prefix = "{$propertyName}.*.";
            } else {
                $prefix = "{$propertyName}.*";
            }
        } else {
            throw new TypeError('Property is not a data object or a collection.');
        }

        if ($definition->properties instanceof Collection) {
            $rules = $this->get($definition);

            return $rules->mapWithKeys(fn (Collection $rules, string $name) => [
                "{$prefix}{$name}" => $rules,
            ])->prepend($parentRules, $propertyName);
        }

        $rules = $this->getRulesForProperty($definition->properties);

        return collect([
            $propertyName => $parentRules,
            $prefix => $rules,
        ]);
    }

    /**
     * Resolve rules for the types.
     *
     * @param Property $property
     * @param Collection $rules
     *
     * @return void
     */
    private function resolveTypes(Property $property, Collection $rules): void
    {
        match ($property->type) {
            'string' => $rules->add('string'),
            'int' => $rules->add('integer'),
            'float' => $rules->add('numeric'),
            'bool' => $rules->add('boolean'),
            'array' => $rules->add('array'),
            default => null,
        };

        if (is_subclass_of($property->type, Collection::class)) {
            $rules->add('array');
        }

        if (is_subclass_of($property->type, BackedEnum::class)) {
            $rules->add('string');
            $rules->add(new Enum($property->type));
        }

        if ($property->type === Carbon::class) {
            $rules->add('date');
        }
    }

    /**
     * Resolve rules for the attributes.
     *
     * @param Property $property
     * @param Collection $data
     *
     * @return void
     */
    private function resolveAttributeRules(Property $property, Collection $data): void
    {
        $property
            ->attributes
            ->filter(fn (object $attribute) => is_subclass_of($attribute, ValidationAttribute::class))
            ->each(function (ValidationAttribute $rule) use ($data) {
                /** @var Collection $rules */
                $rules = $data->get('rules');

                $rules->add($rule->rule);
                if ($rule instanceof Rule && $rule->message !== null) {
                    /** @var Collection $messages */
                    $messages = $data->get('messages');

                    $messages->put($rule->rule, $rule->message);
                }
            });
    }
}
