<?php

namespace Kellton\Tools\Features\Action\Data;

/**
 * Class Result handles the success result of the action.
 */
class Result extends ActionResult
{
    /**
     * Result constructor.
     *
     * @param mixed $data
     */
    public function __construct(
        public readonly mixed $data = null,
    ) {
    }
}
