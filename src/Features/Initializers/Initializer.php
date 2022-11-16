<?php

namespace Kellton\Tools\Features\Initializers;

use Illuminate\Support\Collection;

/**
 * Class Initializer handles base class for initializers.
 */
abstract class Initializer
{
    /**
     * Initializers are executed in ascending order of this constant.
     */
    protected const ORDER = 0;

    /**
     * Returns collection of actions to be executed on initialization.
     *
     * @return Collection
     */
    abstract public function getActions(): Collection;
}
