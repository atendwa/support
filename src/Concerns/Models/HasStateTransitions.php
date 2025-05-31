<?php

declare(strict_types=1);

namespace Atendwa\Support\Concerns\Models;

use Throwable;

trait HasStateTransitions
{
    /**
     * @param  string|array<string>  $values
     */
    public function stateIsNot(string|array $values): bool
    {
        return ! $this->stateIs($values);
    }

    /**
     * @param  string|array<string>  $values
     */
    public function stateIs(string|array $values): bool
    {
        return str($this->state())->is($values, true);
    }

    public function state(): string
    {
        return asString($this->getAttribute('status'));
    }

    public function editable(): bool
    {
        return $this->stateIs($this->editableStates());
    }

    /**
     * @return array<string>
     */
    public function editableStates(): array
    {
        return ['draft'];
    }

    /**
     * @throws Throwable
     */
    public function transition(?string $state = null, bool $persist = false): void
    {
        $this->setAttribute('status', $state ?? $this->nextState());

        when($persist, fn () => $this->update());
    }

    /**
     * @throws Throwable
     */
    public function nextState(): string
    {
        $state = $this->state();
        $map = $this->states();

        throw_if(! array_key_exists($state, $map), 'Invalid transition from: ' . $state);

        throw_if(
            collect($map)->keys()->filter(fn ($key): bool => $key === $state)->count() > 1,
            'Multiple transitions detected!'
        );

        return $map[$state];
    }

    /**
     * @return array<string, string>
     */
    abstract public function states(): array;

    public function rollbackStatus(bool $persist = false): void
    {
        $this->setAttribute('status', $this->previousState());

        when($persist, fn () => $this->update());
    }

    public function previousState(): false|int|string
    {
        return array_search($this->state(), $this->states(), true);
    }

    public function badgeColor(): string
    {
        $state = $this->state();

        if (any([str($state)->contains('pending'), in_array($state, $this->warningStates())])) {
            return 'warning';
        }

        if (in_array($state, $this->dangerStates())) {
            return 'danger';
        }

        if (in_array($state, $this->infoStates())) {
            return 'info';
        }

        if ($state === $this->finalSuccessState()) {
            return 'success';
        }

        return 'gray';
    }

    /**
     * @return array<string>
     */
    public function warningStates(): array
    {
        return ['draft', 'returned'];
    }

    /**
     * @return array<string>
     */
    public function infoStates(): array
    {
        return ['in progress'];
    }

    /**
     * @return array<string>
     */
    public function dangerStates(): array
    {
        return ['rejected', 'flagged'];
    }

    public function isCompleted(): bool
    {
        return $this->stateIs($this->finalSuccessState());
    }

    public function finalSuccessState(): string
    {
        return 'completed';
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => $this->finalSuccessState()]);
    }
}
