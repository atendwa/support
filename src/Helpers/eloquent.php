<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

if (! function_exists('relatedModel')) {

    /**
     * @throws Exception
     */
    function relatedModel(Model $model, string $name): Model
    {
        return modelRelation($model, $name)->getRelated();
    }
}

if (! function_exists('modelRelation')) {
    /**
     * @throws Exception
     */
    function modelRelation(Model $model, string $name): Relation
    {
        if (! method_exists($model, $name)) {
            throw new Exception('Relation:' . $name . ' not found in:' . $model::class);
        }

        $relation = $model->$name();

        if (! $relation instanceof Relation) {
            throw new Exception('Invalid relation:' . $name . ' in:' . $model::class);
        }

        return $relation;
    }
}

if (! function_exists('modelCacheKey')) {
    function modelCacheKey(Model|string $model, string $key, string $delimiter = ':'): string
    {
        return str(class_basename($model))->append($delimiter . $key)->toString();
    }
}
