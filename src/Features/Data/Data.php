<?php

namespace Kellton\Tools\Features\Data;

use Kellton\Tools\Features\Data\Exceptions\MissingConstructor;
use Kellton\Tools\Features\Data\Exceptions\WrongDefaultValue;
use Kellton\Tools\Features\Data\Services\DataService;
use Kellton\Tools\Features\Data\Services\RuleService;
use Illuminate\Validation\ValidationException;
use ReflectionException;

/**
 * Class Data is the base class for all data classes.
 */
readonly abstract class Data
{
    /**
     * Crate data instance from a multiple sources.
     *
     * @param mixed ...$payloads
     *
     * @return static
     *
     * @throws WrongDefaultValue
     * @throws MissingConstructor
     * @throws ReflectionException
     * @throws ValidationException
     */
    public static function create(mixed ...$payloads): static
    {
        /** @var DataService $service */
        $service = app(DataService::class);

        return $service->create(static::class, ...$payloads);
    }

    /**
     * Returns the validation rules.
     * This should be mainly used only for testing.
     *
     * @return array
     *
     * @throws MissingConstructor
     * @throws ReflectionException
     */
    public static function getValidationRules(): array
    {
        /** @var RuleService $service */
        $service = app(RuleService::class);

        return $service->getByClass(static::class)->transform(fn ($value) => $value->get('rules'))->toArray();
    }
}
