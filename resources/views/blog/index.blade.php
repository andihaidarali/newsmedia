<x-public-layout :title="$currentSiteSetting?->site_title ?: config('app.name', 'Laravel')" :description="$currentSiteSetting?->site_description ?: 'Berita terbaru dan artikel pilihan.'" :latest-posts="$latestPosts">
    <main class="mx-auto max-w-7xl px-4 py-6">
        @if ($headlinePost)
            <section class="mb-10">
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
                    <div class="lg:col-span-7">
                        @include('blog.partials.post-card', ['post' => $headlinePost, 'featured' => true])
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:col-span-5 lg:grid-cols-1">
                        @foreach ($latestPosts->take(3) as $post)
                            @include('blog.partials.post-card', ['post' => $post, 'compact' => true])
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
            <div class="lg:col-span-8">
                @php
                    $nextPostsUrl = $posts->hasMorePages()
                        ? $posts->nextPageUrl().(str_contains($posts->nextPageUrl(), '?') ? '&' : '?').'load_more=1'
                        : null;
                @endphp

                <section id="latest-posts" class="mb-10" data-load-more-container>
                    <div class="mb-6 flex items-center justify-between">
                        <h2 class="flex items-center gap-2 text-xl font-black text-[var(--color-secondary)]">
                            <span class="h-6 w-1 rounded-full bg-[var(--color-primary)]"></span>
                            {{ $search ? 'Hasil Pencarian' : 'Berita Terbaru' }}
                        </h2>
                    </div>

                    @if ($search)
                        <p class="mb-4 text-sm text-[var(--color-text-secondary)]">Menampilkan hasil untuk "{{ $search }}".</p>
                    @endif

                    <div data-load-more-list>
                        @forelse ($posts as $post)
                            @include('blog.partials.post-card', ['post' => $post, 'list' => true])
                        @empty
                            <div class="col-span-full rounded-xl border border-dashed border-[var(--color-border)] bg-[var(--color-surface-dark)] p-8 text-center text-sm text-[var(--color-text-muted)]">
                                Belum ada post yang dipublikasikan.
                            </div>
                        @endforelse
                    </div>

                    @if ($nextPostsUrl)
                        <div class="mt-8 flex justify-center">
                            <button type="button" data-load-more-button data-next-url="{{ $nextPostsUrl }}" class="inline-flex items-center rounded-lg bg-[var(--color-secondary)] px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-[var(--color-accent)]">
                                Load More
                            </button>
                        </div>
                    @endif
                </section>

                @foreach ($categorySections as $section)
                    <section class="mb-10">
                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="flex items-center gap-2 text-xl font-black text-[var(--color-secondary)]">
                                <span class="h-6 w-1 rounded-full bg-[var(--color-primary)]"></span>
                                {{ $section['category']->name }}
                            </h2>
                            <a href="{{ route('categories.show', $section['category']) }}" class="text-sm font-semibold text-[var(--color-primary)] hover:underline">Lihat Semua -&gt;</a>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach ($section['posts'] as $post)
                                @include('blog.partials.post-card', ['post' => $post, 'compact' => true])
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            @include('blog.partials.sidebar', ['latestPosts' => $latestPosts, 'popularPosts' => $latestPosts])
        </div>
    </main>
</x-public-layout>
