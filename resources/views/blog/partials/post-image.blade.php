@props([
    'post',
    'class' => 'aspect-[16/9] w-full object-cover',
])

@php
    $youtubeThumbnail = null;

    if ($post->type === 'video' && filled($post->youtube_url)) {
        preg_match('/(?:youtu\.be\/|youtube\.com\/watch\?v=)([\w-]+)/', $post->youtube_url, $matches);
        $videoId = $matches[1] ?? null;
        $youtubeThumbnail = $videoId ? "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg" : null;
    }

    $imageUrl = match ($post->type) {
        'video' => $youtubeThumbnail,
        'infographic' => $post->infographic_image
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($post->infographic_image)
            : null,
        default => $post->featured_image_url,
    };

@endphp

@if ($imageUrl)
    <img src="{{ $imageUrl }}" alt="{{ $post->title }}" class="{{ $class }}">
@else
    <div class="{{ $class }} bg-[var(--color-surface-darker)]"></div>
@endif
