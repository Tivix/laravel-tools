<?php

namespace Kellton\Tools\Tests\Data;

use Kellton\Tools\Features\Data\Attributes\Validation\Rule;
use Kellton\Tools\Features\Data\Data;

readonly class TestData extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        #[Rule('email', message: 'Wrong email address format!')]
        public string $email
    ) {
    }
}
