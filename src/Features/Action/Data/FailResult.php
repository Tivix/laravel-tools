<?php

namespace Kellton\Tools\Features\Action\Data;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class FailResult handles the fail result of the action.
 */
class FailResult extends ActionResult
{
    /**
     * FailResult constructor.
     *
     * @param int $code
     * @param string $message
     */
    public function __construct(
        public readonly int $code,
        public readonly string $message,
    ) {
    }

    /**
     * Check if the result is a not found result.
     *
     * @return bool
     */
    public function isNotFound(): bool
    {
        return $this->code === Response::HTTP_NOT_FOUND;
    }
}
