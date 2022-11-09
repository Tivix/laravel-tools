<?php

namespace Kellton\Tools\Feature\Data\Attributes\Validation;

use Attribute;

/**
 * Class DateTimeFormat handles adding date time format rule for data classes.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class DateTimeFormat extends ValidationAttribute
{
    /**
     * @var string date time format rule
     */
    public readonly string $rule;

    /**
     * DateTimeFormat constructor.
     */
    public function __construct()
    {
        $this->rule = 'date_format:' . config('tools.date.datetime_format');
    }
}
