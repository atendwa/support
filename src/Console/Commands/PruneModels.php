<?php

declare(strict_types=1);

namespace Atendwa\Support\Console\Commands;

use Atendwa\Support\Concerns\Models\Prunable;
use Atendwa\Support\Services\FindClassesUsingTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PruneModels extends Command
{
    protected $signature = 'models:prune';

    protected $description = 'Prune old models from the database';

    public function handle(): void
    {
        $this->info('Pruning models...');

        $models = app(FindClassesUsingTrait::class)->execute(Prunable::class, [
            base_path('components/Filament/Models'),
            base_path('components/Base/Models'),
            base_path('app/Models'),
        ]);

        Artisan::call('model:prune', ['--model' => $models->all()]);

        $this->info('Models pruned successfully.');
    }
}
