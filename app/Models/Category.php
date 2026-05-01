<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Category extends Model
{
    use HasFactory, HasSlug;

    /**
     * The attribute used to generate the slug.
     */
    protected string $slugSource = 'name';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'sort_order',
    ];

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    /**
     * Get the posts belonging to this category.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    /**
     * Get all descendants recursively.
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    public function ancestorsAndSelf(): Collection
    {
        $ancestors = collect();
        $category = $this;

        while ($category) {
            $ancestors->prepend($category);
            $category = $category->parent;
        }

        return $ancestors;
    }

    public function slugPath(): string
    {
        return $this->ancestorsAndSelf()
            ->pluck('slug')
            ->filter()
            ->implode('/');
    }

    public function publicUrl(): string
    {
        return url('/'.$this->slugPath());
    }

    public static function findBySlugPath(string $path): ?self
    {
        $segments = array_values(array_filter(explode('/', trim($path, '/'))));

        if ($segments === []) {
            return null;
        }

        $category = static::query()
            ->whereNull('parent_id')
            ->where('slug', $segments[0])
            ->first();

        foreach (array_slice($segments, 1) as $segment) {
            if (! $category) {
                return null;
            }

            $category = static::query()
                ->where('parent_id', $category->id)
                ->where('slug', $segment)
                ->first();
        }

        return $category;
    }

    public function descendantsAndSelf(): Collection
    {
        return collect([$this])
            ->merge(
                $this->children->flatMap(fn (Category $child) => $child->descendantsAndSelf())
            )
            ->values();
    }
}
