<?php

declare(strict_types=1);

namespace Atendwa\Support\Providers;

use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Builder::macro('whereMorph', function (string $name, ?Model $model = null): object {
            $type = '_type';

            $this->where([$name . $type => $model?->getMorphClass(), $name . '_id' => $model?->getKey()]);

            return $this;
        });

        Http::macro('ping', fn (string $url): bool => Http::get($url)->successful());

        Cache::macro('setIfMissing', function (string $key, mixed $value): void {
            when(Cache::missing($key), fn () => Cache::put($key, $value));
        });

        Cache::macro(
            'refresh',
            function (string $key, Closure|DateInterval|DateTimeInterface|int|null $ttl, mixed $callback): void {
                Cache::forget($key);

                if ($callback instanceof Closure) {
                    Cache::remember($key, $ttl, $callback);

                    return;
                }

                Cache::remember($key, $ttl, fn (): mixed => $callback);
            }
        );

        Cache::macro('push', function (string $key, mixed $value, ?DateTimeInterface $ttl = null): void {
            $target = cache()->get($key);

            cache()->put($key, collect(is_array($target) ? $target : [])->push($value)->toArray(), $ttl);
        });

        Blueprint::macro(
            'trail',
            function (string $name, bool $nullableUser = true, bool $nullableTimestamp = true): void {
                match ($nullableUser) {
                    true => $this->unsignedInteger($name . '_by')->index()->nullable(),
                    false => $this->unsignedInteger($name . '_by')->index(),
                };

                match ($nullableTimestamp) {
                    true => $this->timestamp($name . '_at')->nullable(),
                    false => $this->timestamp($name . '_at'),
                };
            }
        );

        Blueprint::macro('status', fn (string $default = 'draft') => $this->string('status')->default($default));
        Blueprint::macro('slug', fn () => $this->string('slug')->index()->nullable());

        Blueprint::macro('name', function (bool $isUnique = false): void {
            match ($isUnique) {
                true => $this->string('name', 255)->index()->unique(),
                false => $this->string('name', 255)->index(),
            };
        });

        Blueprint::macro('description', fn () => $this->text('description')->nullable());
        Blueprint::macro('default', fn () => $this->boolean('is_default')->default(false)->index());

        Blueprint::macro('uniqueNameInTeam', fn () => $this->unique(['name', 'team_id']));

        Blueprint::macro('tenant', function (bool $nullable = false): void {
            match ($nullable) {
                true => $this->foreignId('tenant_id')->index()->nullable()->constrained()->cascadeOnDelete(),
                false => $this->foreignId('tenant_id')->index()->constrained()->cascadeOnDelete(),
            };
        });

        Blueprint::macro('audit', function (): void {
            $this->boolean('is_active')->default(true)->index();
            $this->string('location')->nullable();
            $this->text('user_agent')->nullable();
            $this->ipAddress()->nullable();
            $this->trail('created', true, false);
            $this->trail('updated', true, false);
            $this->trail('deleted');
            $this->trail('restored');
            $this->trail('archived');
        });
    }
}
