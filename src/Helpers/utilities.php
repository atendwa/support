<?php

declare(strict_types=1);

use Atendwa\Support\Facades\Utilities;
use Illuminate\Support\Carbon;
use Symfony\Component\Finder\SplFileInfo;

if (! function_exists('carbon')) {
    function carbon(): Carbon
    {
        return app(Carbon::class);
    }
}

if (! function_exists('inferFileClass')) {
    /**
     * @throws Throwable
     */
    function inferFileClass(string|SplFileInfo $filePath): string
    {
        return Utilities::inferFileClass($filePath);
    }
}


if (! function_exists('purgeHorizon')) {
    function purgeHorizon(bool $clearQueues = false): void
    {
        when($clearQueues, fn () => Artisan::call('queue:clear'));

        Illuminate\Support\Facades\Redis::connection('horizon')->flushdb();
    }
}
