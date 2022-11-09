<?php

namespace Kellton\Tools\Feature\Data\Attributes\Validation;

use Attribute;

/**
 * Class DateFormat handles adding date format rule for data classes.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class DateFormat extends ValidationAttribute
{
    /**
     * @var string date format rule
     */
    public readonly string $rule;

    /**
     * DateFormat constructor.
     */
    public function __construct()
    {
        $this->rule = 'date_format:' . config('tools.date.format');
    }
}
