<?php

declare(strict_types=1);

namespace Atendwa\Support\Contracts;

use Filament\Forms\Components\Section;
use Filament\Support\Components\Component;
use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;

interface HasMeta
{
    public function metaColumnName(): string;

    /**
     * @return Collection<string, mixed>
     */
    public function meta(): Collection;

    public function getMeta(string $key): mixed;

    /**
     * @param  array<string, mixed>  $data
     *
     * @return Collection<string, mixed>
     */
    public function updateMeta(array $data): Collection;

    /**
     * @return array<string>
     */
    public function sortedMetaAttributes(): array;

    /**
     * @param  array<string, mixed>  $data
     */
    public static function metaValidator(array $data): Validator;

    /**
     * @param  array<string, mixed>|null  $data
     *
     * @return array<string, mixed>
     */
    public function validateMeta(?array $data = null, bool $update = true): array;

    /**
     * @return array<string, mixed>
     */
    public function mergeMeta(): array;

    /**
     * @return array<string>
     */
    public static function metaAttributeValidationRule(string $attribute): array;

    /**
     * @return array<string, string>
     */
    public function metaAttributes(): array;

    public static function metaInputField(string $attribute, string $type): Component;

    public static function metaInputSection(): Section;
}
