<?php

declare(strict_types=1);

use Atendwa\Support\Facades\TypeSafety;

if (! function_exists('asString')) {
    function asString(mixed $value, string $default = ''): string
    {
        return TypeSafety::asString($value, $default);
    }
}

if (! function_exists('asFloat')) {
    function asFloat(mixed $value, float $default = 0): float
    {
        return TypeSafety::asFloat($value, $default);
    }
}

if (! function_exists('asInteger')) {
    function asInteger(mixed $value, int $default = 0): int
    {
        return TypeSafety::asInteger($value, $default);
    }
}

if (! function_exists('asArray')) {
    /**
     * @param  array<(int|string), mixed>  $default
     *
     * @return array<(int|string), mixed>
     */
    function asArray(mixed $value, array $default = []): array
    {
        return TypeSafety::asArray($value, $default);
    }
}

if (! function_exists('arrayOfStrings')) {
    /**
     * @return array<string>
     */
    function arrayOfStrings(mixed $value): array
    {
        return TypeSafety::arrayOfStrings($value);
    }
}

if (! function_exists('asObject')) {
    /**
     * @throws Throwable
     */
    function asObject(object|string $value): object
    {
        return TypeSafety::asObject($value);
    }
}

if (! function_exists('asMixedArray')) {
    /**
     * @param  array<(int|string), mixed>  $value
     *
     * @return array<string, mixed>
     */
    function asMixedArray(array $value): array
    {
        return TypeSafety::asMixedArray($value);
    }
}

if (! function_exists('asInstanceOf')) {
    /**
     * @template T
     *
     * @param  class-string<T>  $class
     *
     * @return T
     *
     * @throws Exception
     */
    function asInstanceOf(mixed $object, string $class, ?string $message = null): mixed
    {
        return TypeSafety::asInstanceOf($object, $class, $message);
    }
}
