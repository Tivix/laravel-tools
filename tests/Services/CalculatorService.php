<?php

namespace Kellton\Tools\Tests\Services;

use Kellton\Tools\Features\Action\Services\ActionService;

/**
 * Class CalculatorService handles example calculations.
 */
class CalculatorService extends ActionService
{
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
}
