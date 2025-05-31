<?php

declare(strict_types=1);

namespace Atendwa\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Iterables extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'atendwa-iterables';
    }
}
