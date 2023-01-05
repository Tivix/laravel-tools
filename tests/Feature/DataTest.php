<?php

namespace Kellton\Tools\Tests\Feature;

use Kellton\Tools\Features\Data\Data;
use Kellton\Tools\Features\Data\Exceptions\MissingConstructor;
use Kellton\Tools\Tests\Data\IndexData;
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
     */
    public function testCreateShouldSucceed(): void
    {
        $data = new TestData('John', 'Doe');
        $this->assertInstanceOf(Data::class, $data);

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
}

/**
 * Class TestData is used for testing readonly Data class.
 */
readonly class TestData extends Data
{
    public function __construct(public string $firstName, public string $lastName)
    {
    }
}
