<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

if (! function_exists('nextModelKey')) {
    /**
     * @throws Throwable
     */
    function nextModelKey(Model $model): int
    {
        if (DB::getDriverName() === 'sqlite') {
            $column = 'id';

            return asInteger(DB::table($model->getTable())->max($column)) + 1;
        }

        $stringable = str("SHOW TABLE STATUS LIKE 'name'")->replace('name', $model->getTable());

        return asInteger(collect((array) DB::select($stringable->toString())[0])->get('Auto_increment'));
    }
}

if (! function_exists('nextModelSlug')) {
    /**
     * @throws Throwable
     */
    function nextModelSlug(Model $model, ?string $prefix = null, int $length = 3, string $padding = '0'): string
    {
        return str((string) nextModelKey($model))->padLeft($length, $padding)
            ->prepend($prefix ?? modelInitials($model))->toString();
    }
}
