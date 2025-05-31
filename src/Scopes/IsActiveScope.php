<?php

declare(strict_types=1);

namespace Atendwa\Support\Scopes;

use Atendwa\Support\Contracts\Toggleable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class IsActiveScope implements Scope
{
    /**
     * @param  Builder<Model>  $builder
     */
    public function apply(Builder $builder, Model $model, bool $active = true): void
    {
        if ($model instanceof Toggleable && $model->isToggleable()) {
            $column = 'is_active';

            $builder->where($column, $active);
        }
    }
}
