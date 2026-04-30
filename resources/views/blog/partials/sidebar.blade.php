@props([
    'latestPosts' => collect(),
    'popularPosts' => collect(),
])

@php
    $rankedPosts = collect($popularPosts)->isNotEmpty() ? collect($popularPosts) : collect($latestPosts);
@endphp

<aside class="lg:col-span-4">
    <div class="sidebar-sticky space-y-8">
        <div class="rounded-xl overflow-hidden">
            <div class="flex h-64 items-center justify-center border border-dashed border-[var(--color-border)] bg-[var(--color-surface-dark)]">
                <span class="text-sm text-[var(--color-text-muted)]">Advertisement</span>
            </div>
        </div>

        <div class="rounded-xl border border-[var(--color-border)] bg-white p-5 shadow-sm">
            <h3 class="mb-4 flex items-center gap-2 text-base font-black text-[var(--color-secondary)]">
                <svg class="h-5 w-5 text-[var(--color-primary)]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.89 1.45l8 8A2 2 0 0121 10.86V20a2 2 0 01-2 2H5a2 2 0 01-2-2v-9.14a2 2 0 01.11-.42l8-8a2 2 0 012.78 0z"/></svg>
                Terpopuler
            </h3>
            <div class="space-y-4">
                @forelse ($rankedPosts->take(5) as $post)
                    <div class="group flex gap-3 items-start">
                        <span class="w-8 shrink-0 text-2xl font-black leading-none {{ $loop->first ? 'text-[var(--color-primary)]' : 'text-[var(--color-surface-darker)]' }}">{{ $loop->iteration }}</span>
                        <div class="min-w-0 flex-1">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-[var(--color-primary)]">{{ $post->category?->name ?? 'News' }}</div>
                            <h4 class="text-sm font-semibold text-[var(--color-text-primary)] transition-colors group-hover:text-[var(--color-primary)]">
                                <a href="{{ $post->publicUrl() }}">{{ $post->title }}</a>
                            </h4>
                            <span class="text-[11px] text-[var(--color-text-muted)]">{{ $post->published_at?->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-[var(--color-text-muted)]">Belum ada post terbaru.</p>
                @endforelse
            </div>
        </div>

        @if ($popularTags->isNotEmpty())
            <div class="rounded-xl border border-[var(--color-border)] bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-base font-black text-[var(--color-secondary)]">Topik Populer</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($popularTags as $tag)
                        <a href="{{ route('tags.show', $tag) }}" class="rounded-full bg-[var(--color-surface-dark)] px-3 py-1.5 text-xs font-medium text-[var(--color-text-secondary)] transition-colors hover:bg-[var(--color-primary)] hover:text-white">
                            {{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($latestPosts->isNotEmpty())
            <div class="rounded-xl border border-[var(--color-border)] bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-base font-black text-[var(--color-secondary)]">Terbaru</h3>
                <div class="space-y-4">
                    @foreach ($latestPosts->take(4) as $post)
                        @include('blog.partials.post-card', ['post' => $post, 'compact' => true])
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</aside>
