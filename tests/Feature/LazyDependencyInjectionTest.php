<?php

namespace Kellton\Tools\Tests\Feature;

use Kellton\Tools\Tests\Services\CalculatorService;
use Kellton\Tools\Tests\TestCase;

/**
 * Class LazyDependencyInjectionTest handles tests for lazy dependency injection.
 */
class LazyDependencyInjectionTest extends TestCase
{
    /**
     * Check if injection succeed.
     *
     * @return void
     */
    public function testInjectionShouldSucceed(): void
    {
        /** @var CalculatorService $service */
        $service = app(CalculatorService::class);

        $this->assertTrue($service->example());
    }
}
