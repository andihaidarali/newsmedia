<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory, HasSlug;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'advertorial_id',
        'type',
        'title',
        'slug',
        'excerpt',
        'body',
        'featured_image',
        'youtube_url',
        'gallery_images',
        'infographic_image',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'gallery_images' => 'array',
        ];
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    /**
     * Get the author of the post.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the category of the post.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function advertorial(): BelongsTo
    {
        return $this->belongsTo(Advertorial::class);
    }

    /**
     * Get the tags attached to the post.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function writerCredits(): HasMany
    {
        return $this->hasMany(PostAuthor::class);
    }

    // ──────────────────────────────────────────────
    // Accessors
    // ──────────────────────────────────────────────

    /**
     * Get the full URL for the featured image.
     */
    protected function featuredImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->featured_image
                ? Storage::disk('public')->url($this->featured_image)
                : null,
        );
    }

    /**
     * Get a short excerpt, auto-generated from body if not set.
     */
    protected function readableExcerpt(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->excerpt ?: str()->limit(strip_tags($this->body), 160),
        );
    }

    // ──────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────

    /**
     * Scope to only published posts.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope to only draft posts.
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope to only scheduled posts.
     */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', 'scheduled')
            ->where('published_at', '>', now());
    }

    /**
     * Scope to filter by category slug.
     */
    public function scopeByCategory(Builder $query, string $slug): Builder
    {
        return $query->whereHas('category', fn (Builder $q) => $q->where('slug', $slug));
    }

    /**
     * Scope to filter by tag slug.
     */
    public function scopeByTag(Builder $query, string $slug): Builder
    {
        return $query->whereHas('tags', fn (Builder $q) => $q->where('slug', $slug));
    }
}
