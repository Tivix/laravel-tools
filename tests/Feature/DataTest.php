<?php

namespace Kellton\Tools\Tests\Feature;

use Illuminate\Validation\ValidationException;
use Kellton\Tools\Features\Data\Data;
use Kellton\Tools\Features\Data\Exceptions\MissingConstructor;
use Kellton\Tools\Features\Data\Exceptions\WrongDefaultValue;
use Kellton\Tools\Tests\Data\EnumCollectionData;
use Kellton\Tools\Tests\Data\IndexData;
use Kellton\Tools\Tests\Data\IntegerCollectionData;
use Kellton\Tools\Tests\Data\StringCollectionData;
use Kellton\Tools\Tests\Data\TestData;
use Kellton\Tools\Tests\Enums\ExampleEnum;
use Kellton\Tools\Tests\TestCase;
use ReflectionException;

/**
 * Class DataTest handles the tests for the data class.
 */
class DataTest extends TestCase
{
    /**
     * Check if create data class succeed.
     *
     * @return void
     *
     * @throws MissingConstructor
     * @throws ReflectionException
     * @throws ValidationException
     * @throws WrongDefaultValue
     */
    public function testCreateShouldSucceed(): void
    {
        $data = TestData::create([
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@kellton.com',
        ]);
        $validationRules = $data::getValidationRules();

        $this->assertIsArray($validationRules);
        $this->assertNotEmpty($validationRules);
    }

    /**
     * Check filters data class succeed.
     *
     * @return void
     *
     * @throws MissingConstructor
     * @throws ReflectionException
     */
    public function testFiltersDataShouldSucceed(): void
    {
        $validationRules = IndexData::getValidationRules();

        $this->assertIsArray($validationRules);
        $this->assertNotEmpty($validationRules);
    }

    /**
     * Check if custom message from rule succeed.
     *
     * @return void
     *
     * @throws MissingConstructor
     * @throws ReflectionException
     * @throws WrongDefaultValue
     */
    public function testRuleMessageShouldSucceed(): void
    {
        try {
            TestData::create([
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'john',
            ]);
        } catch (ValidationException $e) {
            $message = data_get($e->errors(), 'email.0');
            $this->assertSame('Wrong email address format!', $message);
        }
    }

    /**
     * Check if create data with enum collection succeed.
     *
     * @return void
     *
     * @throws MissingConstructor
     * @throws ReflectionException
     * @throws ValidationException
     * @throws WrongDefaultValue
     */
    public function testEnumCollectionShouldSucceed(): void
    {
        $validationRules = EnumCollectionData::getValidationRules();

        $this->assertIsArray($validationRules);
        $this->assertNotEmpty($validationRules);

        $data = EnumCollectionData::create([
            'collection' => [ExampleEnum::BAR->value, ExampleEnum::FOO->value],
        ]);

        $this->assertSame(ExampleEnum::BAR, $data->collection->first());
        $this->assertSame(ExampleEnum::FOO, $data->collection->last());
    }

    /**
     * Check if create data with string collection succeed.
     *
     * @return void
     *
     * @throws MissingConstructor
     * @throws ReflectionException
     * @throws ValidationException
     * @throws WrongDefaultValue
     */
    public function testStringCollectionShouldSucceed(): void
    {
        $validationRules = StringCollectionData::getValidationRules();

        $this->assertIsArray($validationRules);
        $this->assertNotEmpty($validationRules);

        $data = StringCollectionData::create([
            'collection' => ['test', 'test2'],
        ]);

        $this->assertSame('test', $data->collection->first());
        $this->assertSame('test2', $data->collection->last());
    }

    /**
     * Check if create data with integer collection succeed.
     *
     * @return void
     *
     * @throws MissingConstructor
     * @throws ReflectionException
     * @throws ValidationException
     * @throws WrongDefaultValue
     */
    public function testCollectionOfIntegersShouldSucceed(): void
    {
        $validationRules = IntegerCollectionData::getValidationRules();

        $this->assertIsArray($validationRules);
        $this->assertNotEmpty($validationRules);

        $data = IntegerCollectionData::create([
            'collection' => ['1', '2'],
        ]);

        $this->assertSame(1, $data->collection->first());
        $this->assertSame(2, $data->collection->last());
    }
}
