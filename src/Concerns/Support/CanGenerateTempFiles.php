<?php

declare(strict_types=1);

namespace Atendwa\Support\Concerns\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait CanGenerateTempFiles
{
    protected string $filePath;

    protected string $filename;

    protected function generateFile(mixed $contents): void
    {
        File::ensureDirectoryExists(storage_path('app/private/temp-files'));

        $this->filename = Str::random();
        $this->filePath = storage_path(mb_strtolower('app/private/temp-files/' . $this->filename));

        file_put_contents($this->filePath, $contents);
        chmod($this->filePath, 0600);
    }

    protected function cleanFile(): void
    {
        unlink($this->filePath);
    }
}
