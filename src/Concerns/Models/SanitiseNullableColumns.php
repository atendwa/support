<?php

declare(strict_types=1);

namespace Atendwa\Support\Concerns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

trait SanitiseNullableColumns
{
    public static function bootSanitiseNullableColumns(): void
    {
        parent::creating(fn (Model $model) => self::sanitiseValue($model));
        parent::updating(fn (Model $model) => self::sanitiseValue($model));
    }

    /**
     * @return array<string>
     */
    public static function nullableColumns(Model $model): array
    {
        return cache()->rememberForever(self::nullableCacheKey(), fn () => collect(Schema::getColumns($model->getTable()))
            ->filter(fn ($data): bool => is_array($data) && $data['nullable'])->pluck('name')
            ->filter(fn ($value): bool => is_string($value))->all());
    }

    public static function clearNullableColumns(): void
    {
        cache()->forget(self::nullableCacheKey());
    }

    private static function nullableCacheKey(): string
    {
        return str(static::class)->replace('\\', '')->snake()->toString() . '_nullable_columns';
    }

    /**
     * @return string[]
     */
    private static function preservedColumns(): array
    {
        return [];
    }

    private static function sanitiseValue(Model $model): void
    {
        collect($model->getAttributes())->keys()->each(function (string $key) use ($model): void {
            if (! in_array($key, self::nullableColumns($model)) || in_array($key, self::preservedColumns())) {
                return;
            }

            $value = $model->getAttributeValue($key);

            if (is_string($value)) {
                $model->setAttribute($key, filled($value) ? str($value)->trim()->toString() : null);
            }
        });
    }
}
