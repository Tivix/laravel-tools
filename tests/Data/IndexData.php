<?php

namespace Kellton\Tools\Tests\Data;

use Illuminate\Support\Collection;
use Kellton\Tools\Features\Data\Attributes\DataCollection;
use Kellton\Tools\Features\Data\Attributes\MapName;
use Kellton\Tools\Features\Data\Data;
use Kellton\Tools\Undefined;

/**
 * Class IndexData handles the data for any index action with filters.
 */
readonly class IndexData extends Data
{
    /**
     * FilterData constructor.
     *
     * @param Collection|Undefined $filters
     */
    public function __construct(
        #[MapName('f')]
        #[DataCollection(FilterData::class)]
        public Collection|Undefined $filters,
    ) {
    }
}
