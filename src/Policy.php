<?php

declare(strict_types=1);

namespace Atendwa\Support;

use Atendwa\Support\Concerns\Support\UsesPolicySetup;
use Illuminate\Auth\Access\HandlesAuthorization;

class Policy
{
    use HandlesAuthorization;
    use UsesPolicySetup;

    protected ?string $resource = null;
}
