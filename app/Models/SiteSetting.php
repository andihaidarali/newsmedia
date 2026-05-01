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
        'social_links',
    ];

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
        ];
    }

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

    public function socialLinks(): array
    {
        return collect($this->social_links ?? [])
            ->filter(fn ($link) => filled($link['platform'] ?? null) && filled($link['url'] ?? null))
            ->map(fn ($link) => [
                'platform' => (string) $link['platform'],
                'label' => $this->socialPlatformLabel((string) $link['platform']),
                'url' => (string) $link['url'],
            ])
            ->values()
            ->all();
    }

    public function socialPlatformLabel(string $platform): string
    {
        return match ($platform) {
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'youtube' => 'YouTube',
            'x' => 'X',
            'tiktok' => 'TikTok',
            'linkedin' => 'LinkedIn',
            'telegram' => 'Telegram',
            'whatsapp' => 'WhatsApp',
            default => ucfirst($platform),
        };
    }
}
