<?php

declare(strict_types=1);

namespace Atendwa\Support\Facades;

use Illuminate\Support\Facades\Facade;

class TypeSafety extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'atendwa-type-safety';
    }
}
