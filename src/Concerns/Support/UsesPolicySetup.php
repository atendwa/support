<?php

namespace Atendwa\Support\Concerns\Support;

use Atendwa\Support\Contracts\Transitionable;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Throwable;

trait UsesPolicySetup
{
    use HandlesAuthorization;

    protected ?string $resource = null;

    /**
     * @throws Throwable
     */
    public function viewAny(Model $model): bool
    {
        return $model->can($this->permission('view_any'));
    }

    /**
     * @throws Throwable
     */
    public function view(Model $user, Model $model): bool
    {
        return every([$user->can($this->permission('view')), filled($model->getKey())]);
    }

    /**
     * @throws Throwable
     */
    public function create(Model $model): bool
    {
        return $model->can($this->permission('create'));
    }

    /**
     * @throws Throwable
     */
    public function import(Model $model): bool
    {
        return $model->can($this->permission('import'));
    }

    /**
     * @throws Throwable
     */
    public function export(Model $model): bool
    {
        return $model->can($this->permission('export'));
    }

    /**
     * @throws Throwable
     */
    public function update(Model $user, Model $model, bool $bypass = false): bool
    {
        return $this->baseUpdate($user, $model, $bypass);
    }

    /**
     * @throws Throwable
     */
    public function baseUpdate(Model $user, Model $model, bool $bypass = false): bool
    {
        $value = $user->can($this->permission('update'));

        return match ($model instanceof Transitionable && ! $bypass) {
            true => every([$value, $model->editable()]),
            false => $value,
        };
    }

    /**
     * @throws Throwable
     */
    public function delete(Model $user, Model $model): bool
    {
        $value = $user->can($this->permission('delete'));

        return match ($model->hasAttribute('deleted_at')) {
            true => every([$value, blank($model->getAttribute('deleted_at'))]),
            false => $value,
        };
    }

    /**
     * @throws Throwable
     */
    public function deleteAny(Model $model): bool
    {
        return $model->can($this->permission('delete_any'));
    }

    /**
     * @throws Throwable
     */
    public function forceDelete(Model $user, Model $model): bool
    {
        $value = $user->can($this->permission('force_delete'));

        return match ($model->hasAttribute('deleted_at')) {
            true => every([$value, filled($model->getAttribute('deleted_at'))]),
            false => $value,
        };
    }

    /**
     * @throws Throwable
     */
    public function forceDeleteAny(Model $model): bool
    {
        return $model->can($this->permission('force_delete_any'));
    }

    /**
     * @throws Throwable
     */
    public function restore(Model $user, Model $model): bool
    {
        if (! $model->hasAttribute('deleted_at')) {
            return false;
        }

        return every([$user->can($this->permission('restore')), filled($model->getAttribute('deleted_at'))]);
    }

    /**
     * @throws Throwable
     */
    public function restoreAny(Model $model): bool
    {
        return $model->can($this->permission('restoreAny'));
    }

    /**
     * @throws Throwable
     */
    public function replicate(Model $user, Model $model): bool
    {
        return $user->can($this->permission('replicate'), $model);
    }

    /**
     * @throws Throwable
     */
    public function custom(Model $model, string $perm): bool
    {
        return $model->can($this->permission($perm));
    }

    /**
     * @throws Throwable
     */
    private function permission(string $prefix): string
    {
        return $prefix . '_' . FilamentShield::getPermissionIdentifier($this->getResource());
    }

    /**
     * @throws Throwable
     */
    private function getResource(): string
    {
        if (filled($this->resource)) {
            return $this->resource;
        }

        $stringable = str(static::class);
        $namespace = $stringable->before('\\Policies')->append('\\Filament\\Resources\\')->toString();

        $resource = str('{namespace}{name}Resource')->replace('{namespace}', $namespace)
            ->replace('{name}', $stringable->between('\\Policies\\', 'Policy')->toString())
            ->toString();

        throw_if(! class_exists($resource), 'Resource not found for policy: ' . $stringable);

        return $resource;
    }
}