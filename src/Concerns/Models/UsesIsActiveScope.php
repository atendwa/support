<?php

declare(strict_types=1);

namespace Atendwa\Support\Concerns\Models;

use Atendwa\Support\Scopes\IsActiveScope;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait UsesIsActiveScope
{
    public static function bootUsesIsActiveScope(): void
    {
        static::addGlobalScope(new IsActiveScope());
    }

    /**
     * @param  Builder<$this>  $builder
     *
     * @return Builder<$this>
     */
    #[Scope]
    public function active(Builder $builder, bool $active = true): Builder
    {
        $column = 'is_active';

        return $builder->where($column, $active);
    }

    public function isToggleable(): bool
    {
        return true;
    }
}
