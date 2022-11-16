<?php

namespace Kellton\Tools\Features\Action\Exceptions;

use Exception;
use Kellton\Tools\Features\Action\Data\FailResult;
use Throwable;

/**
 * Class MandatoryResult handles exceptions when a Result needs to be successful, but is not.
 */
class MandatoryResult extends Exception
{
    /**
     * MandatoryResult constructor.
     *
     * @param FailResult $result
     * @param Throwable|null $previous
     */
    public function __construct(FailResult $result, ?Throwable $previous = null)
    {
        parent::__construct('Mandatory result expected!', $result->code, $previous);
    }
}
