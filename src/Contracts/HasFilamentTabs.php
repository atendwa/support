<?php

namespace Atendwa\Support\Contracts;

interface HasFilamentTabs
{
    /**
     * @return string[]
     */
    public static function getFilamentTabs(): array;

    public function finalSuccessState(): string;
}
