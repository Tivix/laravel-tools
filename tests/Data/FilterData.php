<?php

namespace Kellton\Tools\Tests\Data;

use Kellton\Tools\Features\Data\Attributes\MapName;
use Kellton\Tools\Features\Data\Data;

/**
 * Class FilterData handles filtering data.
 */
class FilterData extends Data
{
    /**
     * FilterData constructor.
     *
     * @param string $name
     * @param string $operation
     * @param string $value
     */
    public function __construct(
        #[MapName('n')]
        public readonly string $name,
        #[MapName('o')]
        public readonly string $operation,
        #[MapName('v')]
        public readonly string $value,
    ) {
    }
}


