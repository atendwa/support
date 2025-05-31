<?php

declare(strict_types=1);

namespace Atendwa\Support\Concerns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

trait HasModelUtilities
{
    public function string(mixed $key): string
    {
        return asString($this->getAttribute($key));
    }

    public function integer(mixed $key): int
    {
        return asInteger($this->getAttribute($key));
    }

    public function float(mixed $key): float
    {
        return asFloat($this->getAttribute($key));
    }

    public function id(): int
    {
        return (int) $this->string('id');
    }

    public function isInactive(): bool
    {
        return ! $this->isActive();
    }

    public function date(string $key): ?Carbon
    {
        if ($this->isBlank($key)) {
            return null;
        }

        return Carbon::parse($this->string($key));
    }

    public function isActive(): bool
    {
        return boolval($this->getAttribute('is_active'));
    }

    public function name(bool $plural = false): string
    {
        $name = str($this->string('name'))->title();

        if ($plural) {
            return $name->plural()->toString();
        }

        return $name->toString();
    }

    public function asModel(): Model
    {
        return $this;
    }

    public function isFilled(string $attribute): bool
    {
        return filled($this->getAttribute($attribute));
    }

    public function isBlank(string $attribute): bool
    {
        return ! $this->isFilled($attribute);
    }

    public function description(): string
    {
        return $this->string('description');
    }

    /**
     * @return array<(int|string), string>
     */
    public static function toSelectOption(string $label, string $key = 'id'): array
    {
        return self::query()->select([$key, $label])->pluck($label, $key)
            ->mapWithKeys(fn ($value, $key) => [(int) $key => asString($value)])
            ->all();
    }
}
