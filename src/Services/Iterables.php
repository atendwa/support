<?php

declare(strict_types=1);

namespace Atendwa\Support\Services;

class Iterables
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function retrieve(array $payload, string $map, mixed $default): mixed
    {
        $value = $payload;

        collect(explode('.', $map))->each(function (string|int $key) use (&$value): void {
            $value = match (is_array($value)) {
                true => $value[$key] ?? [],
                false => null,
            };
        });

        return $value ?? $default;
    }

    /**
     * @param  array<(int|string), mixed>  $iterable
     * @param  array<string>  $keys
     *
     * @return array<string, mixed>
     */
    public function sort(iterable $iterable, array $keys): array
    {
        $iterable = collect($iterable);

        return collect($keys)->map(fn ($key) => [$key => $iterable->get($key)])->collapse()->all();
    }
}
