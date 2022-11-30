<?php

namespace Kellton\Tools\Enums;

/**
 * Class FilterOperator handles filter operation.
 */
enum FilterOperator: string
{
    case EQUAL = 'eq';
    case LIKE = 'like';
    case IN = 'in';
}
