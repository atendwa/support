<?php

declare(strict_types=1);

namespace Atendwa\Support\Console\Commands;

use Atendwa\Support\Concerns\Models\Prunable;
use Atendwa\Support\Services\FindClassesUsingTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearHorizon extends Command
{
    protected $signature = 'horizon:purge {--clear-queues : Also clear the queues}';

    protected $description = 'Clear Horizon data and optionally clear queues';

    public function handle(): void
    {
        purgeHorizon((bool) $this->option('clear-queues'));
    }
}
