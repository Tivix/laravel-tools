<?php

namespace Kellton\Tools\Tests\Feature;

use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Kellton\Tools\Features\Data\Attributes\DataCollection;
use Kellton\Tools\Features\Data\Attributes\Validation\Rule;
use Kellton\Tools\Features\Data\Data;
use Kellton\Tools\Features\Data\Exceptions\MissingConstructor;
use Kellton\Tools\Features\Data\Exceptions\WrongDefaultValue;
use Kellton\Tools\Tests\Data\FilterData;
use Kellton\Tools\Tests\Data\IndexData;
use Kellton\Tools\Tests\TestCase;
use Kellton\Tools\Undefined;
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

    /**
     * Check if providing rule message succeed.
     *
     * @return void
     *
     * @throws MissingConstructor
     * @throws ReflectionException
     * @throws WrongDefaultValue
     */
    public function testRuleMessageShouldSucceed(): void
    {
        $data = new class(5) extends Data
        {
            public function __construct(
                #[Rule('regex:/^[0-9]*[0,5]$/', message: 'The :attribute must be a number and divisible by 5.')]
                public int $percentage,
            ) {
            }
        };

        try {
            $data::create(['percentage' => 4]);
        } catch (ValidationException $exception) {
            $message = data_get($exception->errors(), 'percentage.0');
            $this->assertSame('The percentage must be a number and divisible by 5.', $message);
        }
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
