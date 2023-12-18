<?php

namespace Kellton\Tools\Tests\Services;

use Kellton\Tools\Features\Action\Services\ActionService;
use Kellton\Tools\Features\Dependency\Attributes\Dependency;

/**
 * Class CalculatorService handles example calculations.
 */
class CalculatorService extends ActionService
{
    #[Dependency]
    protected ExampleService $exampleService;

    /**
     * Return sum of two numbers.
     *
     * @param int $a
     * @param int $b
     *
     * @return int
     */
    public function add(int $a, int $b): int
    {
        return $this->action(function () use ($a, $b) {
            return $a + $b;
        });
    }

    /**
     * Return division of two numbers.
     *
     * @param int $a
     * @param int $b
     *
     * @return float|int
     */
    public function divide(int $a, int $b): float|int
    {
        return $this->action(function () use ($a, $b) {
            return $a / $b;
        });
    }

    /**
     * Return example.
     *
     * @return bool
     */
    public function example(): bool
    {
        return $this->action(function () {
            return $this->exampleService->example();
        });
    }
}
