<?php

declare(strict_types=1);

namespace Atendwa\Support\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface Toggleable
{
    /**
     * @param  Builder<Model>  $builder
     *
     * @return Builder<Model>
     */
    public function active(Builder $builder, bool $active = true): Builder;

    public function isToggleable(): bool;
}
