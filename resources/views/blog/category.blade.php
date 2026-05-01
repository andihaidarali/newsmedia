<x-public-layout :title="'Category: '.$category->name" :description="'Published posts in '.$category->name.'.'" :latest-posts="$latestPosts">
    <main class="mx-auto max-w-7xl px-4 py-6">
        <div class="mb-8">
            <nav class="mb-4 flex items-center text-sm text-[var(--color-text-muted)]">
                <a href="{{ route('home') }}" class="hover:text-[var(--color-primary)]">Beranda</a>
                @foreach ($category->ancestorsAndSelf() as $breadcrumbCategory)
                    <span class="mx-2 breadcrumb-separator"></span>
                    @if ($loop->last)
                        <span class="text-[var(--color-text-secondary)]">{{ $breadcrumbCategory->name }}</span>
                    @else
                        <a href="{{ $breadcrumbCategory->publicUrl() }}" class="hover:text-[var(--color-primary)]">{{ $breadcrumbCategory->name }}</a>
                    @endif
                @endforeach
            </nav>
            <div class="flex items-center gap-3">
                <span class="h-10 w-1.5 rounded-full bg-[var(--color-primary)]"></span>
                <div>
                    <h1 class="text-3xl font-black text-[var(--color-secondary)]">{{ $category->name }}</h1>
                    @if ($category->description)
                        <p class="mt-1 text-sm text-[var(--color-text-secondary)]">{{ $category->description }}</p>
                    @endif
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
                        Belum ada artikel di kategori ini.
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
