@props([
    'post',
    'class' => 'aspect-[16/9] w-full object-cover',
])

@php
    $imageUrl = $post->featured_image_url ?: $currentSiteSetting?->defaultFeaturedImageUrl();
@endphp

@if ($imageUrl)
    <img src="{{ $imageUrl }}" alt="{{ $post->title }}" class="{{ $class }}">
@else
    <div class="{{ $class }} flex items-center justify-center bg-gradient-to-br from-[var(--color-surface-darker)] to-[var(--color-surface-dark)] text-sm font-semibold uppercase text-[var(--color-text-muted)]">
        {{ $post->category?->name ?? 'News' }}
    </div>
@endif
