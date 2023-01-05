<?php

namespace Kellton\Tools\Features\Action\Data;

/**
 * Class Result handles the success result of the action.
 */
readonly class Result extends ActionResult
{
    /**
     * Result constructor.
     *
     * @param mixed $data
     */
    public function __construct(
        public mixed $data = null,
    ) {
    }
}
