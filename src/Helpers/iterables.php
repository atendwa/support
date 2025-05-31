<?php

declare(strict_types=1);

use Atendwa\Support\Facades\Iterables;

if (! function_exists('sortIterable')) {
    /**
     * @param  array<(int|string), mixed>  $iterable
     * @param  array<string>  $keys
     *
     * @return array<string, mixed>
     */
    function sortIterable(iterable $iterable, array $keys): array
    {
        return Iterables::sort($iterable, $keys);
    }
}

if (! function_exists('retrieve')) {
    /**
     * @param  array<string, mixed>  $iterable
     */
    function retrieve(array $iterable, string $map, mixed $default): mixed
    {
        return Iterables::retrieve($iterable, $map, $default);
    }
}
