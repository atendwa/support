<?php

declare(strict_types=1);

namespace Atendwa\Support\Contracts;

use Illuminate\Database\Eloquent\Model;

interface Auditable
{
    // todo: incomplete

    public function trail(string $trail, ?int $authId = null, bool $persist = false): void;

    public function asModel(): Model;

    public function canFilterByAuthor(): bool;
}
