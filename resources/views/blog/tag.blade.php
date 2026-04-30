<x-public-layout :title="'Tag: '.$tag->name" :description="'Published posts tagged with '.$tag->name.'.'" :latest-posts="$latestPosts">
    <main class="mx-auto max-w-7xl px-4 py-6">
        <div class="mb-8">
            <nav class="mb-4 flex items-center text-sm text-[var(--color-text-muted)]">
                <a href="{{ route('home') }}" class="hover:text-[var(--color-primary)]">Beranda</a>
                <span class="mx-2 breadcrumb-separator"></span>
                <span class="text-[var(--color-text-secondary)]">Topik</span>
            </nav>
            <div class="flex items-center gap-3">
                <span class="h-10 w-1.5 rounded-full bg-[var(--color-primary)]"></span>
                <div>
                    <h1 class="text-3xl font-black text-[var(--color-secondary)]">{{ $tag->name }}</h1>
                    <p class="mt-1 text-sm text-[var(--color-text-secondary)]">Kumpulan artikel dengan topik {{ $tag->name }}.</p>
                </div>
            </div>
        </div>

        @php
            $nextPostsUrl = $posts->hasMorePages()
                ? $posts->nextPageUrl().(str_contains($posts->nextPageUrl(), '?') ? '&' : '?').'load_more=1'
                : null;
        @endphp

        <div id="latest-posts" data-load-more-container>
            <div data-load-more-list>
                @forelse ($posts as $post)
                    @include('blog.partials.post-card', ['post' => $post, 'list' => true])
                @empty
                    <div class="col-span-full py-20 text-center text-lg text-[var(--color-text-muted)]">
                        Belum ada artikel dengan topik ini.
                    </div>
                @endforelse
            </div>

            @if ($nextPostsUrl)
                <div class="mt-10 flex justify-center">
                    <button type="button" data-load-more-button data-next-url="{{ $nextPostsUrl }}" class="inline-flex items-center rounded-lg bg-[var(--color-secondary)] px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-[var(--color-accent)]">
                        Load More
                    </button>
                </div>
            @endif
        </div>
    </main>
</x-public-layout>
