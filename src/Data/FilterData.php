<?php

namespace Kellton\Tools\Data;

use Kellton\Tools\Enums\FilterOperation;
use Kellton\Tools\Feature\Data\Attributes\MapName;
use Kellton\Tools\Feature\Data\Data;

/**
 * Class FilterData handles filtering data.
 */
class FilterData extends Data
{
    /**
     * FilterData constructor.
     *
     * @param string $name
     * @param FilterOperation $operation
     * @param string $value
     */
    public function __construct(
        #[MapName('n')]
        public readonly string $name,
        #[MapName('o')]
        public readonly FilterOperation $operation,
        #[MapName('v')]
        public readonly string $value,
    ) {
    }
}
