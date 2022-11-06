<?php

namespace Kellton\Tools\Feature\Dependency\Attributes;

use Attribute;

/**
 * Class Dependency handles attribute to add dependency using service container.
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Dependency
{
    /**
     * @var string
     */
    private string $class;

    /**
     * @var string|null
     */
    private ?string $field;

    /**
     * Dependency constructor.
     *
     * @param string $class
     * @param string|null $field
     */
    public function __construct(string $class, ?string $field = null)
    {
        $this->class = $class;
        $this->field = $field;
    }

    /**
     * Get dependency class.
     *
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * Get field name.
     *
     * @return string
     */
    public function getField(): string
    {
        if (!$this->field) {
            $path = explode('\\', $this->class);
            $this->field = lcfirst(array_pop($path));
        }

        return $this->field;
    }
}
