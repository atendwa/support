<?php

declare(strict_types=1);

namespace Atendwa\Support\Console\Commands;

use Illuminate\Console\Command;

class ClearHorizon extends Command
{
    protected $signature = 'horizon:purge {--clear-queues : Also clear the queues}';

    protected $description = 'Clear Horizon data and optionally clear queues';

    public function handle(): void
    {
        purgeHorizon((bool) $this->option('clear-queues'));
    }
}
