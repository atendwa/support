<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Validator;

if (! function_exists('headline')) {
    function headline(mixed $value): string
    {
        return str(asString($value))->headline()->toString();
    }
}

if (! function_exists('every')) {
    /**
     * @param  array<int, mixed>  $values
     */
    function every(array $values): bool
    {
        return collect($values)->every(fn ($value): bool => boolval($value));
    }
}

if (! function_exists('any')) {
    /**
     * @param  array<int, bool|int>  $values
     */
    function any(array $values): bool
    {
        return collect($values)->contains(fn ($value): bool => boolval($value));
    }
}

if (! function_exists('modelInitials')) {
    /**
     * @param  class-string|Model  $model
     */
    function modelInitials(string|Model $model): string
    {
        return mb_strtoupper(str(class_basename($model))->snake()->explode('_')
            ->map(fn (string $part): string => $part[0])->implode(''));
    }
}

if (! function_exists('validatorErrorString')) {
    function validatorErrorString(Validator $validator): string
    {
        $errors = collect($validator->errors()->messages())->collapse()->implode(',');

        return str($errors)->replace('.,', ', ')->lower()->toString();
    }
}

if (! function_exists('defaultAuditColumns')) {
    /**
     * @return string[]
     */
    function defaultAuditColumns(): array
    {
        return [
            'restored_at', 'archived_at', 'created_by', 'updated_by', 'deleted_by', 'restored_by', 'archived_by',
            'is_active', 'location', 'user_agent', 'ip_address', 'created_at', 'updated_at', 'deleted_at',
        ];
    }
}
