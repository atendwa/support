<?php

namespace Atendwa\Support\Concerns\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

trait HasDisposableColumns
{
    public function clearSelectableColumns(): void
    {
        cache()->forget($this->selectableColumnsCacheKey());
    }

    #[Scope]
    protected function minimal(Builder $query, array $except = [], bool $fresh = false): Builder
    {
        when($fresh, fn () => $this->clearSelectableColumns());

        return $query->select($this->fetchSelectableColumns($except));
    }

    /**
     * @param  string[]  $except
     *
     * @return string[]
     */
    private function fetchSelectableColumns(array $except = []): array
    {
        return cache()->rememberForever($this->selectableColumnsCacheKey(), fn () => $this->SelectableColumns($except));
    }

    /**
     * @return string[]
     */
    private function SelectableColumns(array $except = []): array
    {
        $exclude = array_unique(array_merge($this->disposableColumns(), $except));

        return array_values(array_diff(Schema::getColumnListing($this->getTable()), $exclude));
    }

    private function selectableColumnsCacheKey(): string
    {
        return str(class_basename($this::class) . ':selectable_columns')->snake()->toString();
    }

    /**
     * @return string[]
     */
    private function disposableColumns(): array
    {
        return [
            'archived_by', 'archived_at', 'location', 'user_agent', 'ip_address',
            'deleted_by', 'deleted_at', 'restored_by', 'restored_at',
        ];
    }
}
