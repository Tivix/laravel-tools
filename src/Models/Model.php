<?php

namespace Kellton\Tools\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Kellton\Tools\Builders\Builder;
use Kellton\Tools\Models\Traits\HasTableName;

/**
 * Class Model handles the base model.
 */
abstract class Model extends EloquentModel
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
