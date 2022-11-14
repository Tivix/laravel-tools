<?php

namespace Kellton\Tools\Models\Traits;

/**
 * Trait HasTableName handles adding method for getting table name of a model.
 */
trait HasTableName
{
    /**
     * Returns table name.
     *
     * @return string
     */
    final public static function getTableName(): string
    {
        return (new (static::class)())->getTable();
    }
}
