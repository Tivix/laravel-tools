<?php

namespace Kellton\Tools\Tests\Feature;

use Kellton\Tools\Tests\Services\CalculatorService;
use Kellton\Tools\Tests\TestCase;

/**
 * Class ActionServiceTest handles the tests for the action service class.
 */
class ActionServiceTest extends TestCase
{
    /**
     * Check if create data class succeed.
     *
     * @return void
     */
    public function testAddShouldSucceed(): void
    {
        $service = new CalculatorService();

        $this->assertEquals(3, $service->add(1, 2));
    }
}
