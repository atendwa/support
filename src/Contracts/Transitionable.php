<?php

declare(strict_types=1);

namespace Atendwa\Support\Contracts;

interface Transitionable
{
    /**
     * @param  string|array<string>  $values
     */
    public function stateIsNot(string|array $values): bool;

    /**
     * @param  string|array<string>  $values
     */
    public function stateIs(string|array $values): bool;

    public function state(): string;

    public function editable(): bool;

    /**
     * @return array<string>
     */
    public function editableStates(): array;

    public function transition(?string $state = null, bool $persist = false): void;

    public function nextState(): string;

    /**
     * @return array<string, string>
     */
    public function states(): array;

    public function rollbackStatus(bool $persist = false): void;

    public function previousState(): false|int|string;

    public function badgeColor(): string;

    /**
     * @return array<string>
     */
    public function warningStates(): array;

    /**
     * @return array<string>
     */
    public function infoStates(): array;

    /**
     * @return array<string>
     */
    public function dangerStates(): array;

    public function isCompleted(): bool;

    public function markAsCompleted(): void;
}
