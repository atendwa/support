<?php

declare(strict_types=1);

namespace Atendwa\Support\Providers;

use Atendwa\Support\Console\Commands\ClearHorizon;
use Atendwa\Support\Console\Commands\PruneModels;
use Atendwa\Support\Services\Iterables;
use Atendwa\Support\Services\TypeSafety;
use Atendwa\Support\Services\Utilities;
use Illuminate\Support\ServiceProvider;

class SupportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('atendwa-type-safety', fn (): TypeSafety => new TypeSafety());
        $this->app->singleton('atendwa-iterables', fn (): Iterables => new Iterables());
        $this->app->singleton('atendwa-utilities', fn (): Utilities => new Utilities());

        $this->app->register(MacroServiceProvider::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([PruneModels::class, ClearHorizon::class]);
        }
    }
}
