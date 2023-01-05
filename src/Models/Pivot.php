<?php

namespace Kellton\Tools\Models;

use Illuminate\Database\Eloquent\Relations\Pivot as EloquentPivot;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Kellton\Tools\Builders\Builder;
use Kellton\Tools\Models\Traits\HasTableName;

/**
 * Class Pivot handles the base pivot model.
 */
abstract class Pivot extends EloquentPivot implements ModelInterface
{
    use HasTableName;

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param QueryBuilder $query
     *
     * @return Builder
     */
    public function newEloquentBuilder($query): Builder
    {
        return new Builder($query);
    }
}
