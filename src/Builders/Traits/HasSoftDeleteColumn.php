<?php

namespace Kellton\Tools\Builders\Traits;

/**
 * Trait HasSoftDeleteColumn handles adding methods for deleted at column to builder.
 *
 * @method self whereDeletedAt($value)
 * @method self withTrashed()
 * @method self withoutTrashed()
 * @method self onlyTrashed()
 */
trait HasSoftDeleteColumn
{
}
