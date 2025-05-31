<?php

declare(strict_types=1);

namespace Atendwa\Support\Concerns\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;

trait Prunable
{
    use MassPrunable;

    /**
     * @return Builder<static>
     */
    public function prunable(): Builder
    {
        return static::query()->where('created_at', '<=', now()->subMonths($this->retentionMonths()));
    }

    protected function retentionMonths(): int
    {
        return 3;
    }
}
