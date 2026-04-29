<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Boot the HasSlug trait.
     *
     * Automatically generates a unique slug from the model's
     * "sluggable" attribute (title or name) on creation.
     */
    public static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $source = $model->{$model->getSlugSource()};
                $model->slug = $model->generateUniqueSlug($source);
            }
        });

        static::updating(function ($model) {
            $source = $model->getSlugSource();
            if ($model->isDirty($source) && ! $model->isDirty('slug')) {
                $model->slug = $model->generateUniqueSlug($model->{$source});
            }
        });
    }

    /**
     * Get the attribute name used to generate the slug.
     */
    public function getSlugSource(): string
    {
        return property_exists($this, 'slugSource') ? $this->slugSource : 'title';
    }

    /**
     * Generate a unique slug for this model.
     */
    public function generateUniqueSlug(string $value): string
    {
        $slug = Str::slug($value);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }

    /**
     * Get the route key name for Laravel route-model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
