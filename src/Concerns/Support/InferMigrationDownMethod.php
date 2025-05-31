<?php

declare(strict_types=1);

namespace Atendwa\Support\Concerns\Support;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Stringable;
use ReflectionClass;
use RuntimeException;
use Throwable;

trait InferMigrationDownMethod
{
    /**
     * @throws Throwable
     */
    public function down(): void
    {
        Schema::dropIfExists($this->table()->toString());
    }

    /**
     * @throws Throwable
     */
    protected function table(): Stringable
    {
        $filename = (new ReflectionClass(self::class))->getFileName();

        if (! is_string($filename)) {
            throw new RuntimeException('Unable to determine the migration filename.');
        }

        return str($filename)->between('create_', '_table');
    }
}
