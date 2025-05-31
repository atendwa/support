<?php

declare(strict_types=1);

namespace Atendwa\Support\Services;

use Exception;
use Throwable;

class TypeSafety
{
    public function asString(mixed $value, string $default = ''): string
    {
        return match (true) {
            is_numeric($value) => (string) $value,
            is_string($value) => $value,
            default => $default,
        };
    }

    public function asFloat(mixed $value, float $default = 0): float
    {
        return match (is_float($value)) {
            true => $value,  false => $default
        };
    }

    public function asInteger(mixed $value, int $default = 0): int
    {
        return match (is_int($value)) {
            true => $value,  false => $default
        };
    }

    /**
     * @param  array<(int|string), mixed>  $default
     *
     * @return array<(int|string), mixed>
     */
    public function asArray(mixed $value, array $default = []): array
    {
        return match (is_array($value)) {
            true => $value,  false => $default
        };
    }

    /**
     * @return array<string>
     */
    public function arrayOfStrings(mixed $value): array
    {
        return collect(is_array($value) ? $value : [])->filter(fn ($value): bool => is_string($value))->all();
    }

    /**
     * @throws Throwable
     */
    public function asObject(object|string $class): object
    {
        if (is_object($class)) {
            return $class;
        }

        $object = app($class);

        throw_if(! is_object($object), 'Object not found for ' . $class);

        return $object;
    }

    /**
     * @template T
     *
     * @param  class-string<T>  $class
     *
     * @return T
     *
     * @throws Exception
     */
    public function asInstanceOf(mixed $object, string $class, ?string $message = null): mixed
    {
        if ($object instanceof $class) {
            return $object;
        }

        $stringable = str('Expected instance of {expectation} but got {class}!')
            ->replace('{class}', is_object($object) ? $object::class : 'null')
            ->replace('{expectation}', $class);

        throw new Exception($message ?? $stringable->toString());
    }

    /**
     * @param  array<(int|string), mixed>  $value
     *
     * @return array<string, mixed>
     */
    public function asMixedArray(array $value): array
    {
        $value = collect($value);

        return $value->keys()->map(fn ($key) => [asString($key) => $value->get($key)])->collapse()->all();
    }
}
