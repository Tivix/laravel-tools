<?php

namespace Kellton\Tools\Data;

use Illuminate\Support\Collection;
use Kellton\Tools\Enums\SortDirection;
use Kellton\Tools\Features\Data\Attributes\DataCollection;
use Kellton\Tools\Features\Data\Attributes\DefaultValue;
use Kellton\Tools\Features\Data\Attributes\MapName;
use Kellton\Tools\Features\Data\Attributes\Validation\Rule;
use Kellton\Tools\Features\Data\Data;
use Kellton\Tools\Undefined;

/**
 * Class QueryData handles filtering and sorting data.
 *
 * @method static self create(mixed ...$payloads)
 */
class QueryData extends Data
{
    /**
     * QueryData constructor.
     *
     * @param Collection|Undefined $filters
     * @param string|Undefined $sortBy
     * @param SortDirection|Undefined $sortDirection
     * @param int|Undefined $page
     * @param int|Undefined $perPage
     */
    public function __construct(
        #[MapName('f')]
        #[DataCollection(FilterData::class)]
        public Collection|Undefined $filters,
        #[MapName('s')]
        public string|Undefined $sortBy,
        #[MapName('d')]
        public SortDirection|Undefined $sortDirection,
        #[MapName('p')]
        #[Rule('min:1')]
        public int|Undefined $page,
        #[MapName('l')]
        public int|Undefined $perPage,
    ) {
    }
}
