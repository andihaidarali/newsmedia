@props([
    'post',
    'compact' => false,
    'featured' => false,
    'list' => false,
])

@php
    $categoryName = $post->category?->name ?? 'News';
    $publishedLabel = $post->published_at?->translatedFormat('d M Y') ?? '-';
@endphp

@if ($featured)
    <article class="article-card group relative overflow-hidden rounded-2xl bg-white shadow-sm border border-[var(--color-border)] lg:col-span-2">
        <a href="{{ $post->publicUrl() }}" class="block">
            <div class="gradient-overlay aspect-[16/10] w-full bg-[var(--color-surface-darker)]">
                @include('blog.partials.post-image', ['post' => $post, 'class' => 'h-full w-full object-cover transition-transform duration-700 group-hover:scale-105'])
            </div>
            <div class="absolute inset-x-0 bottom-0 z-10 p-6">
                <span class="category-badge mb-3">{{ $categoryName }}</span>
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
        </a>
    </article>
@elseif ($list)
    <article class="group border-b border-[var(--color-border)] py-4 first:pt-0">
        <div class="flex items-start gap-4">
            <div class="min-w-0 flex-1">
                <div class="text-xs font-bold uppercase tracking-wider text-[var(--color-primary)]">{{ $categoryName }}</div>
                <h3 class="mt-1 text-[1.05rem] font-bold leading-snug text-[var(--color-text-primary)] transition-colors group-hover:text-[var(--color-primary)] md:text-[1.15rem]">
                    <a href="{{ $post->publicUrl() }}">{{ $post->title }}</a>
                </h3>
                <div class="mt-2 text-sm text-[var(--color-text-secondary)]">{{ $post->published_at?->translatedFormat('d F Y H:i') ?? '-' }}</div>
            </div>
            <a href="{{ $post->publicUrl() }}" class="block h-[98px] w-[98px] shrink-0 overflow-hidden rounded-md bg-[var(--color-surface-darker)] sm:h-[112px] sm:w-[112px]">
                @include('blog.partials.post-image', ['post' => $post, 'class' => 'h-full w-full object-cover transition-transform duration-500 group-hover:scale-105'])
            </a>
        </div>
    </article>
@elseif ($compact)
    <article class="group flex gap-3 items-start">
        <a href="{{ $post->publicUrl() }}" class="block w-20 h-20 shrink-0 overflow-hidden rounded-lg bg-[var(--color-surface-darker)]">
            @include('blog.partials.post-image', ['post' => $post, 'class' => 'h-full w-full object-cover transition-transform duration-500 group-hover:scale-110'])
        </a>
        <div class="min-w-0 flex-1">
            <div class="text-[10px] font-bold uppercase tracking-wider text-[var(--color-primary)]">{{ $categoryName }}</div>
            <h3 class="mt-1 line-clamp-2 text-sm font-semibold leading-snug text-[var(--color-text-primary)] transition-colors group-hover:text-[var(--color-primary)]">
                <a href="{{ $post->publicUrl() }}">{{ $post->title }}</a>
            </h3>
            <div class="mt-1 text-[11px] text-[var(--color-text-muted)]">{{ $publishedLabel }}</div>
        </div>
    </article>
@else
    <article class="article-card group overflow-hidden rounded-xl bg-white border border-[var(--color-border)] shadow-sm">
        <a href="{{ $post->publicUrl() }}" class="block">
            <div class="relative aspect-[16/10] overflow-hidden bg-[var(--color-surface-darker)]">
                @include('blog.partials.post-image', ['post' => $post, 'class' => 'h-full w-full object-cover transition-transform duration-700 group-hover:scale-110'])
            </div>
            <div class="p-4">
                <div class="text-[10px] font-bold uppercase tracking-wider text-[var(--color-primary)]">{{ $categoryName }}</div>
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
