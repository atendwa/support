<?php

declare(strict_types=1);

namespace Atendwa\Support\Services;

use Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use SplFileInfo;
use Throwable;

class Utilities
{
    /**
     * @throws Throwable
     */
    public function inferFileClass(string|SplFileInfo $file): string
    {
        $file = $file instanceof SplFileInfo ? $file->getRealPath() : $file;

        $class = str($file)->between(base_path() . '/', '.php')->replace('/', '\\')->ucfirst()->toString();

        throw_if(! class_exists($class), 'Class:' . $class . ' from file: ' . $file . ' not Found!');

        return $class;
    }

    /**
     * @return Collection<string, mixed>
     *
     * @throws ConnectionException
     */
    public function ipLookup(string $ip): Collection
    {
        return Http::asForm()->post('https://iplocation.com/', ['ip' => trim($ip)])->collect();
    }
}
