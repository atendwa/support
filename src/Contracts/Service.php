<?php

declare(strict_types=1);

namespace Atendwa\Support\Contracts;

interface Service
{
    public static function make(): self;
}
