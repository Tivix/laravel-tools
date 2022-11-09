<?php

namespace Kellton\Tools\Feature\Data\Services;

use BackedEnum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Kellton\Tools\Feature\Data\Data;
use Kellton\Tools\Feature\Data\Definition;
use Kellton\Tools\Feature\Data\Exceptions\MissingConstructor;
use Kellton\Tools\Feature\Data\Exceptions\WrongDefaultValue;
use Kellton\Tools\Feature\Data\Property;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Kellton\Tools\Undefined;
use ReflectionException;

/**
 * Class DataService handles the data objects.
 */
final class DataService
{
    /**
     * DataService constructor.
     *
     * @param DefinitionService $definitionService
     * @param RuleService $ruleService
     */
    public function __construct(
        private readonly DefinitionService $definitionService,
        private readonly RuleService $ruleService
    ) {
    }

    /**
     * Returns a new data instance from a multiple sources.
     *
     * @param class-string $class
     * @param mixed $payload
     *
     * @return Data
     *
     * @throws MissingConstructor
     * @throws ReflectionException
     * @throws ValidationException
     * @throws WrongDefaultValue
     */
    public function create(string $class, mixed ...$payload): Data
    {
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        if (is_array($payload) && count($payload) === 1) {
            $payload = $payload[0];
        }

        if ($payload instanceof Request) {
            $payload = array_merge($payload->route()->parameters, $payload->all());
        }

        $payload = collect_all($payload);
        $definition = $this->definitionService->get($class);

        $properties = $this->validate($definition, $payload);

        $this->resolveMapProperties($definition, $properties);
        $this->resolveDefaultValues($definition, $properties);
        $this->resolveCastValues($definition, $properties);

        return $definition->properties
            ->mapWithKeys(function (Property $parameter) use ($properties) {
                return [$parameter->name => $properties->get($parameter->name)];
            })
            ->pipe(fn (Collection $parameters) => new $definition->class(...$parameters));
    }

    /**
     * Returns validated data or throws an validation exception.
     *
     * @param Definition $definition
     * @param Collection $payload
     *
     * @return Collection
     *
     * @throws MissingConstructor
     * @throws ReflectionException
     * @throws ValidationException
     */
    private function validate(Definition $definition, Collection $payload): Collection
    {
        $rules = $this->ruleService->get($definition);

        $validator = Validator::make($payload->toArray(), $rules->toArray());

        $validator->validate();

        return collect_all($validator->validated());
    }

    /**
     * Resolve map properties.
     *
     * @param Definition $definition
     * @param Collection $properties
     *
     * @return void
     */
    private function resolveMapProperties(Definition $definition, Collection $properties): void
    {
        $definition->properties->each(function (Property $property) use ($properties) {
            if ($property->mapName === null) {
                return;
            }

            if ($properties->has($property->mapName)) {
                $properties->put($property->name, $properties->get($property->mapName));
                $properties->forget($property->mapName);
            }
        });
    }

    /**
     * Resolve default values.
     *
     * @param Definition $definition
     * @param Collection $properties
     *
     * @return void
     * @throws WrongDefaultValue
     */
    private function resolveDefaultValues(Definition $definition, Collection $properties): void
    {
        $definition->properties
            ->filter(fn (Property $property) => !$properties->has($property->name))
            ->each(function (Property $property) use (&$properties) {
                if (!$property->isUndefined && $property->defaultValue instanceof Undefined) {
                    throw new WrongDefaultValue($property);
                }
                $properties->put($property->name, $property->defaultValue);
            });
    }

    /**
     * Resolve cast values.
     *
     * @param Definition $definition
     * @param Collection $properties
     *
     * @return void
     */
    private function resolveCastValues(Definition $definition, Collection $properties): void
    {
        $properties->transform(function ($value, $name) use ($definition) {
            $property = $definition->properties->first(fn (Property $object) => $object->name === $name);

            if ($property === null) {
                return $value;
            }

            if ($value === null || $value instanceof Undefined) {
                return $value;
            }

            return $this->cast($property, $value);
        });
    }

    /**
     * Returns casted value.
     *
     * @param Property $property
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws MissingConstructor
     * @throws ReflectionException
     * @throws ValidationException
     * @throws WrongDefaultValue
     */
    private function cast(Property $property, mixed $value): mixed
    {
        $shouldCast = !is_object($value) || $property->dataClass;
        if (!$shouldCast) {
            return $value;
        }

        if (is_subclass_of($property->type, BackedEnum::class)) {
            return $property->type::from($value);
        }

        if ($property->type === Carbon::class) {
            return Carbon::parse($value);
        }

        /** @var class-string<Data> $class */
        $class = $property->dataClass;

        if ($property->isDataObject) {
            return $class::create($value);
        }

        if ($property->isCollection) {
            return (clone $value)->map(function ($item) use ($class) {
                return $class::create($item);
            });
        }

        return $value;
    }
}
