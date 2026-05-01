@props([
    'post',
    'compact' => false,
    'featured' => false,
    'list' => false,
])

@php
    $categoryName = $post->category?->name ?? 'News';
    $publishedLabel = $post->published_at?->translatedFormat('d M Y') ?? '-';
    $hasRenderableMedia = match ($post->type) {
        'video' => filled($post->youtube_url),
        'infographic' => filled($post->infographic_image),
        default => filled($post->featured_image),
    };
    $typeLabels = [
        'video' => 'Video',
        'gallery' => 'Gallery',
        'infographic' => 'Infografis',
    ];
    $typeLabel = $typeLabels[$post->type] ?? null;
    $showsMediaOverlay = in_array($post->type, ['video', 'gallery'], true);
    $isVideoCard = $post->type === 'video';
@endphp

@if ($featured)
    <article class="article-card group relative overflow-hidden rounded-2xl bg-white shadow-sm border border-[var(--color-border)] lg:col-span-2">
        <a href="{{ $post->publicUrl() }}" class="block">
            <div class="{{ $isVideoCard ? 'relative aspect-[16/10] w-full bg-[var(--color-surface-darker)]' : 'gradient-overlay aspect-[16/10] w-full bg-[var(--color-surface-darker)]' }}">
                @include('blog.partials.post-image', ['post' => $post, 'class' => 'h-full w-full object-cover transition-transform duration-700 group-hover:scale-105'])
                @if ($showsMediaOverlay)
                    <span class="absolute {{ $isVideoCard ? 'left-1/2 top-1/2 h-16 w-16 -translate-x-1/2 -translate-y-1/2' : 'left-4 top-4 h-11 w-11' }} z-10 inline-flex items-center justify-center rounded-full bg-black/60 text-white backdrop-blur-sm" aria-label="{{ $post->type === 'video' ? 'Play video' : 'Open gallery' }}">
                        @if ($post->type === 'video')
                            <svg class="ml-1 h-7 w-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.14v13.72c0 .76.82 1.24 1.5.87l10-6.86a1 1 0 000-1.74l-10-6.86A1 1 0 008 5.14z"/></svg>
                        @else
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="14" height="14" rx="2"></rect><path d="M17 8h4v11a2 2 0 01-2 2H8v-4"></path></svg>
                        @endif
                    </span>
                @endif
            </div>
            @if ($isVideoCard)
                <div class="p-6">
                    <div class="mb-3 flex flex-wrap items-center gap-2">
                        <span class="category-badge">{{ $categoryName }}</span>
                        @if ($typeLabel)
                            <span class="content-type-badge">{{ $typeLabel }}</span>
                        @endif
                    </div>
                    <h2 class="text-2xl font-black leading-tight text-[var(--color-secondary)] transition-colors group-hover:text-[var(--color-primary)] md:text-3xl">
                        {{ $post->title }}
                    </h2>
                    <p class="mt-2 line-clamp-2 text-sm text-[var(--color-text-secondary)]">{{ $post->readable_excerpt }}</p>
                    <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-[var(--color-text-muted)]">
                        <span>{{ $post->author?->name ?? 'Unknown' }}</span>
                        <span>&bull;</span>
                        <span>{{ $publishedLabel }}</span>
                    </div>
                </div>
            @else
                <div class="absolute inset-x-0 bottom-0 z-10 p-6">
                    <div class="mb-3 flex flex-wrap items-center gap-2">
                        <span class="category-badge">{{ $categoryName }}</span>
                        @if ($typeLabel)
                            <span class="content-type-badge content-type-badge-on-dark">{{ $typeLabel }}</span>
                        @endif
                    </div>
                    <h2 class="mt-2 text-2xl font-black leading-tight text-white transition-colors group-hover:text-[var(--color-primary)] md:text-3xl">
                        {{ $post->title }}
                    </h2>
                    <p class="mt-2 line-clamp-2 text-sm text-gray-300">{{ $post->readable_excerpt }}</p>
                    <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-gray-400">
                        <span>{{ $post->author?->name ?? 'Unknown' }}</span>
                        <span>&bull;</span>
                        <span>{{ $publishedLabel }}</span>
                    </div>
                </div>
            @endif
        </a>
    </article>
@elseif ($list)
    <article class="group border-b border-[var(--color-border)] py-4 first:pt-0">
        <div class="flex items-start {{ $hasRenderableMedia ? 'gap-4' : 'gap-0' }}">
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <div class="text-xs font-bold uppercase tracking-wider text-[var(--color-primary)]">{{ $categoryName }}</div>
                    @if ($typeLabel)
                        <span class="content-type-badge">{{ $typeLabel }}</span>
                    @endif
                </div>
                <h3 class="mt-1 text-[1.05rem] font-bold leading-snug text-[var(--color-text-primary)] transition-colors group-hover:text-[var(--color-primary)] md:text-[1.15rem]">
                    <a href="{{ $post->publicUrl() }}">{{ $post->title }}</a>
                </h3>
                <div class="mt-2 text-sm text-[var(--color-text-secondary)]">{{ $post->published_at?->translatedFormat('d F Y H:i') ?? '-' }}</div>
            </div>
            @if ($hasRenderableMedia)
                <a href="{{ $post->publicUrl() }}" class="relative block h-[98px] w-[98px] shrink-0 overflow-hidden rounded-md bg-[var(--color-surface-darker)] sm:h-[112px] sm:w-[112px]">
                    @include('blog.partials.post-image', ['post' => $post, 'class' => 'h-full w-full object-cover transition-transform duration-500 group-hover:scale-105'])
                    @if ($showsMediaOverlay)
                        <span class="absolute left-2 top-2 z-10 inline-flex h-8 w-8 items-center justify-center rounded-full bg-black/60 text-white backdrop-blur-sm" aria-label="{{ $post->type === 'video' ? 'Play video' : 'Open gallery' }}">
                            @if ($post->type === 'video')
                                <svg class="ml-0.5 h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.14v13.72c0 .76.82 1.24 1.5.87l10-6.86a1 1 0 000-1.74l-10-6.86A1 1 0 008 5.14z"/></svg>
                            @else
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="14" height="14" rx="2"></rect><path d="M17 8h4v11a2 2 0 01-2 2H8v-4"></path></svg>
                            @endif
                        </span>
                    @endif
                </a>
            @endif
        </div>
    </article>
@elseif ($compact)
    <article class="group flex items-start {{ $hasRenderableMedia ? 'gap-3' : 'gap-0' }}">
        @if ($hasRenderableMedia)
            <a href="{{ $post->publicUrl() }}" class="relative block h-20 w-20 shrink-0 overflow-hidden rounded-lg bg-[var(--color-surface-darker)]">
                @include('blog.partials.post-image', ['post' => $post, 'class' => 'h-full w-full object-cover transition-transform duration-500 group-hover:scale-110'])
                @if ($showsMediaOverlay)
                    <span class="absolute left-2 top-2 z-10 inline-flex h-7 w-7 items-center justify-center rounded-full bg-black/60 text-white backdrop-blur-sm" aria-label="{{ $post->type === 'video' ? 'Play video' : 'Open gallery' }}">
                        @if ($post->type === 'video')
                            <svg class="ml-0.5 h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.14v13.72c0 .76.82 1.24 1.5.87l10-6.86a1 1 0 000-1.74l-10-6.86A1 1 0 008 5.14z"/></svg>
                        @else
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="14" height="14" rx="2"></rect><path d="M17 8h4v11a2 2 0 01-2 2H8v-4"></path></svg>
                        @endif
                    </span>
                @endif
            </a>
        @endif
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-1.5">
                <div class="text-[10px] font-bold uppercase tracking-wider text-[var(--color-primary)]">{{ $categoryName }}</div>
                @if ($typeLabel)
                    <span class="content-type-badge content-type-badge-compact">{{ $typeLabel }}</span>
                @endif
            </div>
            <h3 class="mt-1 line-clamp-2 text-sm font-semibold leading-snug text-[var(--color-text-primary)] transition-colors group-hover:text-[var(--color-primary)]">
                <a href="{{ $post->publicUrl() }}">{{ $post->title }}</a>
            </h3>
            <div class="mt-1 text-[11px] text-[var(--color-text-muted)]">{{ $publishedLabel }}</div>
        </div>
    </article>
@else
    <article class="article-card group overflow-hidden rounded-xl bg-white border border-[var(--color-border)] shadow-sm">
        <a href="{{ $post->publicUrl() }}" class="block">
            @if ($hasRenderableMedia)
                <div class="relative aspect-[16/10] overflow-hidden bg-[var(--color-surface-darker)]">
                    @include('blog.partials.post-image', ['post' => $post, 'class' => 'h-full w-full object-cover transition-transform duration-700 group-hover:scale-110'])
                    @if ($showsMediaOverlay)
                        <span class="absolute {{ $isVideoCard ? 'left-1/2 top-1/2 h-14 w-14 -translate-x-1/2 -translate-y-1/2' : 'left-3 top-3 h-10 w-10' }} z-10 inline-flex items-center justify-center rounded-full bg-black/60 text-white backdrop-blur-sm" aria-label="{{ $post->type === 'video' ? 'Play video' : 'Open gallery' }}">
                            @if ($post->type === 'video')
                                <svg class="ml-1 h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.14v13.72c0 .76.82 1.24 1.5.87l10-6.86a1 1 0 000-1.74l-10-6.86A1 1 0 008 5.14z"/></svg>
                            @else
                                <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="14" height="14" rx="2"></rect><path d="M17 8h4v11a2 2 0 01-2 2H8v-4"></path></svg>
                            @endif
                        </span>
                    @endif
                </div>
            @endif
            <div class="p-4">
                <div class="flex flex-wrap items-center gap-2">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-[var(--color-primary)]">{{ $categoryName }}</div>
                    @if ($typeLabel)
                        <span class="content-type-badge">{{ $typeLabel }}</span>
                    @endif
                </div>
                <h3 class="mt-1 line-clamp-2 text-base font-bold leading-snug text-[var(--color-text-primary)] transition-colors group-hover:text-[var(--color-primary)]">
                    {{ $post->title }}
                </h3>
                <p class="mt-2 line-clamp-2 text-sm text-[var(--color-text-secondary)]">{{ $post->readable_excerpt }}</p>
                <div class="mt-3 flex items-center justify-between gap-3 text-xs text-[var(--color-text-muted)]">
                    <span class="truncate">{{ $post->author?->name ?? 'Unknown' }}</span>
                    <span class="shrink-0">{{ $publishedLabel }}</span>
                </div>
            </div>
        </a>
    </article>
@endif
