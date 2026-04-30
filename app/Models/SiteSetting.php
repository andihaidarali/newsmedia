<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_title',
        'subtitle',
        'site_description',
        'site_logo',
        'site_favicon',
        'default_featured_image',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }

    public function siteLogoUrl(): ?string
    {
        return $this->site_logo ? Storage::disk('public')->url($this->site_logo) : null;
    }

    public function siteFaviconUrl(): ?string
    {
        return $this->site_favicon ? Storage::disk('public')->url($this->site_favicon) : null;
    }

    public function defaultFeaturedImageUrl(): ?string
    {
        return $this->default_featured_image ? Storage::disk('public')->url($this->default_featured_image) : null;
    }
}
