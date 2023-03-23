<?php

namespace Kellton\Tools\Tests\Data;

use Illuminate\Support\Collection;
use Kellton\Tools\Features\Data\Attributes\CollectionOf;
use Kellton\Tools\Features\Data\Data;
use Kellton\Tools\Tests\Enums\ExampleEnum;

/**
 * Class EnumCollectionData handles the data definition of a enum collection.
 */
readonly class EnumCollectionData extends Data
{
    public function __construct(
        #[CollectionOf(ExampleEnum::class)]
        public Collection $collection,
    ) {
    }
}
