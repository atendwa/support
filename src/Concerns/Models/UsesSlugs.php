<?php

declare(strict_types=1);

namespace Atendwa\Support\Concerns\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

trait UsesSlugs
{
    use HasSlug;

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->saveSlugsTo('slug')->generateSlugsFrom($this->getSlugFrom());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getSlugFrom(): string
    {
        return 'name';
    }
}
