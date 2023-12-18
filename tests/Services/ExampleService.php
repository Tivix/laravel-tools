<?php

namespace Kellton\Tools\Tests\Services;

use Kellton\Tools\Features\Action\Services\ActionService;

/**
 * Class ExampleService handles example service.
 */
class ExampleService extends ActionService
{
    /**
     * Return example content.
     *
     * @return bool
     */
    public function example(): bool
    {
        return $this->action(function () {
            return true;
        });
    }
}
