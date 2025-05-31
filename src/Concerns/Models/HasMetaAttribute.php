<?php

declare(strict_types=1);

namespace Atendwa\Support\Concerns\Models;

use Atendwa\Support\Contracts\HasMeta;
use Exception;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Support\Components\Component;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;
use InvalidArgumentException;
use Throwable;

trait HasMetaAttribute
{
    /**
     * @throws Exception
     */
    public static function bootHasMetaAttribute(): void
    {
        static::creating(fn (Model $model) => self::updateOrCreateMeta($model));
        static::updating(fn (Model $model) => self::updateOrCreateMeta($model));
    }

    public function metaColumnName(): string
    {
        return 'meta';
    }

    /**
     * @return Collection<string, mixed>
     *
     * @throws Throwable
     */
    public function meta(): Collection
    {
        $key = $this->metaColumnName();

        throw_if(
            ! in_array($key, array_keys($this->getCasts())),
            'Attribute: ' . $key . ' must be casted to json / array'
        );

        $meta = $this->getAttribute($key);

        $meta = match ($meta instanceof Collection) {
            false => json_decode(json_encode($meta), true),
            true => collect($meta),
        };

        throw_if(! $meta instanceof Collection, 'Attribute: ' . $key . ' must be an array');

        return $meta->merge($this->mergeMeta());
    }

    /**
     * @throws Throwable
     */
    public function getMeta(string $key): mixed
    {
        $meta = $this->meta();

        throw_if(! $meta->has($key), 'Key:' . $key . ' not found in the mata store!');

        return $meta->get($key);
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @return Collection<string, mixed>
     *
     * @throws Throwable
     */
    public function updateMeta(array $data): Collection
    {
        $this->update([$this->metaColumnName() => $this->meta()->merge($data)]);

        return $this->meta();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    abstract public static function metaValidator(array $data): Validator;

    /**
     * @param  array<string, mixed>|null  $data
     *
     * @return array<string, mixed>
     */
    public function validateMeta(?array $data = null, bool $update = true): array
    {
        $validator = self::metaValidator($data ?? $this->getAttribute($this->metaColumnName()));

        $validator->validate();

        when($update, fn () => $this->setAttribute($this->metaColumnName(), $validator->validated()));

        return $validator->validated();
    }

    /**
     * @return array<string>
     */
    public static function metaAttributeValidationRule(string $attribute): array
    {
        $validator = self::metaValidator([]);

        $rules = $validator->getRules();

        if (! isset($rules[$attribute])) {
            throw new InvalidArgumentException('Validation rules not found for meta attribute: ' . $attribute);
        }

        return $rules[$attribute];
    }

    /**
     * @return array<string, mixed>
     */
    public function mergeMeta(): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    abstract public function metaAttributes(): array;

    public static function metaInputField(string $attribute, string $type): Component
    {
        $rules = self::metaAttributeValidationRule($attribute);

        return (match ($type) {
            default => TextInput::make($attribute)->label(headline($attribute)),
            'array' => KeyValue::make($attribute)->columnSpanFull()->reorderable()->keyLabel('Property name')
                ->valueLabel('Property value')->addActionLabel('Add property'),
            'password' => TextInput::make($attribute)->password()->revealable(),
            'numeric' => TextInput::make($attribute)->numeric(),
            'dateTime' => DateTimePicker::make($attribute),
            'boolean', 'bool' => Toggle::make($attribute),
            'time' => TimePicker::make($attribute),
            'date' => DatePicker::make($attribute),
        })->rules($rules)->label(headline($attribute))
            ->formatStateUsing(function ($record = null) use ($attribute) {
                if (blank($record)) {
                    return null;
                }

                return asInstanceOf($record, HasMeta::class)->meta()->get($attribute);
            })
            ->required(in_array('required', $rules));
    }

    public static function metaInputSection(): Section
    {
        $static = new static();
        $attributes = $static->metaAttributes();

        $sort = $static->sortedMetaAttributes();

        if (filled($sort)) {
            $attributes = sortIterable($attributes, $static->sortedMetaAttributes());
        }

        $fields = collect($attributes)
            ->mapWithKeys(fn ($type, $attribute) => [$attribute => static::metaInputField($attribute, $type)]);

        return Section::make('Properties')->schema($fields->values()->all())
            ->collapsible(fn (string $context): bool => $context === 'view')
            ->icon('heroicon-o-variable')->columns();
    }

    /**
     * @return array<string>
     */
    public function sortedMetaAttributes(): array
    {
        return [];
    }

    /**
     * @throws Exception
     */
    private static function updateOrCreateMeta(Model $model): void
    {
        $hasMeta = asInstanceOf($model, HasMeta::class);

        $data = $hasMeta->validateMeta(self::prepareMeta($model, $hasMeta), $model->exists);

        collect(collect($hasMeta->metaAttributes())->keys()->all())->each(function ($attribute) use ($model): void {
            unset($model->$attribute);
        });

        $model->setAttribute($hasMeta->metaColumnName(), $data);
    }

    /**
     * @return array<string, string>
     */
    private static function prepareMeta(Model $model, HasMeta $hasMeta): array
    {
        $attributes = collect($hasMeta->metaAttributes())->keys()->all();

        $meta = collect($model->toArray())->only($attributes);

        return $hasMeta->meta()
            ->mapWithKeys(fn ($value, $key) => [$key => $meta->get($key, $value)])
            ->only($attributes)->all();
    }
}
