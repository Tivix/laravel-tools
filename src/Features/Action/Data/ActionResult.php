<?php

namespace Kellton\Tools\Features\Action\Data;

use Kellton\Tools\Features\Action\Exceptions\MandatoryResult;
use Kellton\Tools\Features\Data\Data;
use RuntimeException;

/**
 * Class ActionResult handles the result of the action.
 */
abstract class ActionResult extends Data
{
    /**
     * Returns result data if successful.
     *
     * @throws MandatoryResult
     */
    public function getResult(): mixed
    {
        if ($this instanceof Result) {
            return $this->data;
        }

        if ($this instanceof FailResult) {
            throw new MandatoryResult($this);
        }

        throw new RuntimeException('This should never happen!');
    }

    /**
     * Returns result if successful.
     *
     * @return $this
     *
     * @throws MandatoryResult
     */
    public function mandatory(): static
    {
        if ($this instanceof FailResult) {
            throw new MandatoryResult($this);
        }

        return $this;
    }
}
