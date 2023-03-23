<?php

namespace Kellton\Tools\Features\Data\Services;

use BackedEnum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Kellton\Tools\Features\Data\Data;
use Kellton\Tools\Features\Data\Definition;
use Kellton\Tools\Features\Data\Enums\BuildInType;
use Kellton\Tools\Features\Data\Exceptions\MissingConstructor;
use Kellton\Tools\Features\Data\Exceptions\WrongDefaultValue;
use Kellton\Tools\Features\Data\Property;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Kellton\Tools\Undefined;
use LogicException;
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

        if ($payload === null) {
            $payload = [];
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
        $definitions = $this->ruleService->get($definition);

        $rules = $definitions->map(fn (Collection $value) => $value->get('rules'));
        $messages = $definitions->map(fn (Collection $value) => $value->get('messages'));

        $validator = Validator::make(
            $payload->toArray(),
            $rules->toArray(),
            $this->parseValidationErrorMessages($messages)
        );

        $validator->validate();

        return collect_all($validator->validated());
    }

    /**
     * Parse validation error messages to array.
     *
     * @param Collection $validationErrorMessages
     *
     * @return array
     */
    private function parseValidationErrorMessages(Collection $validationErrorMessages): array
    {
        $validationMessages = [];

        $validationErrorMessages->each(function (Collection $errorMessages, $fieldName) use (&$validationMessages) {
            if ($errorMessages->isNotEmpty()) {
                $errorMessages->each(function ($errorMessage, $ruleName) use ($fieldName, &$validationMessages) {
                    $keyName = $fieldName . '.' . explode(':', $ruleName)[0];
                    $validationMessages[$keyName] = $errorMessage;
                });
            }
        });

        return $validationMessages;
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
        $shouldCast = !is_object($value) || $property->dataClass || $property->collectionType;
        if (!$shouldCast) {
            return $value;
        }

        if (is_subclass_of($property->type, BackedEnum::class)) {
            return $property->type::from($value);
        }

        if ($property->type === Carbon::class) {
            return Carbon::parse($value);
        }

        if ($property->isDataObject) {
            /** @var class-string<Data> $class */
            $class = $property->dataClass;

            return $class::create($value);
        }

        if ($property->isCollection) {
            /** @var BackedEnum|Data $class */
            $class = $property->collectionType;

            return (clone $value)->map(function ($item) use ($class) {

                if (is_subclass_of($class, Data::class)) {
                    return $class::create($item);
                }

                if (is_subclass_of($class, BackedEnum::class)) {
                    return $class::from($item);
                }

                if ($class instanceof BuildInType) {
                    return match ($class) {
                        BuildInType::int => (int)$item,
                        BuildInType::string => (string)$item,
                    };
                }

                throw new LogicException('Invalid type');
            });
        }

        return $value;
    }
}
