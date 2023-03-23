<?php

namespace Kellton\Tools\Tests\Data;

use Illuminate\Support\Collection;
use Kellton\Tools\Features\Data\Attributes\CollectionOfStrings;
use Kellton\Tools\Features\Data\Data;

/**
 * Class StringCollectionData handles the data definition of a string collection.
 */
readonly class StringCollectionData extends Data
{
    public function __construct(
        #[CollectionOfStrings]
        public Collection $collection,
    ) {
    }
}
