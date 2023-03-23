<?php

namespace Kellton\Tools\Tests\Data;

use Illuminate\Support\Collection;
use Kellton\Tools\Features\Data\Attributes\CollectionOfIntegers;
use Kellton\Tools\Features\Data\Data;

/**
 * Class IntegerCollectionData handles the data definition of a integer collection.
 */
readonly class IntegerCollectionData extends Data
{
    public function __construct(
        #[CollectionOfIntegers]
        public Collection $collection,
    ) {
    }
}
