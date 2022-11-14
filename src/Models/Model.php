<?php

namespace Kellton\Tools\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Kellton\Tools\Models\Traits\HasTableName;

/**
 * Class Model handles the base model.
 */
abstract class Model extends EloquentModel
{
    use HasTableName;
}
