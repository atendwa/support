<?php

declare(strict_types=1);

namespace Atendwa\Support;

use Atendwa\Support\Concerns\Support\UsesPolicySetup;

class Policy
{
    use UsesPolicySetup;

    protected ?string $resource = null;
}
