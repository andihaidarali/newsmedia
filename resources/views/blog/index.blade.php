<x-public-layout :title="$currentSiteSetting?->site_title ?: config('app.name', 'Laravel')" :description="$currentSiteSetting?->site_description ?: 'Berita terbaru dan artikel pilihan.'" :latest-posts="$latestPosts">
    <main class="mx-auto max-w-7xl px-4 py-6">
        @if ($headlinePosts->isNotEmpty())
            <section
                class="mb-10"
                x-data="{
                    currentSlide: 0,
                    totalSlides: {{ $headlinePosts->count() }},
                    autoplayHandle: null,
                    touchStartX: null,
                    touchEndX: null,
                    startAutoplay() {
                        if (this.totalSlides <= 1 || this.autoplayHandle) {
                            return;
                        }

                        this.autoplayHandle = setInterval(() => {
                            this.nextSlide();
                        }, 5000);
                    },
                    stopAutoplay() {
                        if (! this.autoplayHandle) {
                            return;
                        }

                        clearInterval(this.autoplayHandle);
                        this.autoplayHandle = null;
                    },
                    nextSlide() {
                        this.currentSlide = this.currentSlide === this.totalSlides - 1 ? 0 : this.currentSlide + 1;
                    },
                    previousSlide() {
                        this.currentSlide = this.currentSlide === 0 ? this.totalSlides - 1 : this.currentSlide - 1;
                    },
                    handleTouchStart(event) {
                        this.touchStartX = event.changedTouches[0]?.clientX ?? null;
                        this.touchEndX = null;
                        this.stopAutoplay();
                    },
                    handleTouchEnd(event) {
                        this.touchEndX = event.changedTouches[0]?.clientX ?? null;

                        if (this.touchStartX === null || this.touchEndX === null) {
                            this.startAutoplay();

                            return;
                        }

                        const swipeDistance = this.touchStartX - this.touchEndX;

                        if (Math.abs(swipeDistance) >= 40) {
                            if (swipeDistance > 0) {
                                this.nextSlide();
                            } else {
                                this.previousSlide();
                            }
                        }

                        this.startAutoplay();
                    },
                }"
                x-init="startAutoplay()"
                x-on:mouseenter="stopAutoplay()"
                x-on:mouseleave="startAutoplay()"
            >
                <div class="overflow-hidden rounded-2xl bg-[var(--color-secondary)] shadow-sm">
                    <div class="relative" x-on:touchstart.passive="handleTouchStart($event)" x-on:touchend.passive="handleTouchEnd($event)">
                        @foreach ($headlinePosts as $post)
                            <article
                                x-show="currentSlide === {{ $loop->index }}"
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0 scale-[1.02]"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-[0.99]"
                                class="group relative"
                            >
                                <a href="{{ $post->publicUrl() }}" class="block">
                                    <div class="relative aspect-[16/10] bg-[var(--color-surface-darker)] md:aspect-[21/9]">
                                        @include('blog.partials.post-image', ['post' => $post, 'class' => 'h-full w-full object-cover transition-transform duration-700 group-hover:scale-[1.02]'])
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent"></div>
                                        <div class="absolute inset-x-0 bottom-0 z-10 p-5 md:p-8">
                                            <div class="mb-3 flex flex-wrap items-center gap-2">
                                                <span class="category-badge">{{ $post->category?->name ?? 'News' }}</span>
                                                @if ($post->type !== 'article')
                                                    <span class="content-type-badge content-type-badge-on-dark">
                                                        {{ match ($post->type) {
                                                            'video' => 'Video',
                                                            'gallery' => 'Gallery',
                                                            'infographic' => 'Infografis',
                                                            default => 'Artikel',
                                                        } }}
                                                    </span>
                                                @endif
                                            </div>
                                            <h2 class="max-w-4xl text-2xl font-black leading-tight text-white transition-colors group-hover:text-[var(--color-primary)] md:text-4xl">
                                                {{ $post->title }}
                                            </h2>
                                            <p class="mt-3 max-w-3xl line-clamp-2 text-sm text-gray-200 md:text-base">{{ $post->readable_excerpt }}</p>
                                            <div class="mt-4 flex flex-wrap items-center gap-3 text-xs text-gray-300 md:text-sm">
                                                <span>{{ $post->author?->name ?? 'Unknown' }}</span>
                                                <span>&bull;</span>
                                                <span>{{ $post->published_at?->translatedFormat('d F Y H:i') ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        @endforeach

                        @if ($headlinePosts->count() > 1)
                            <button type="button" x-on:click="previousSlide()" class="absolute left-3 top-1/2 z-20 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-black/55 text-white backdrop-blur-sm transition hover:bg-black/75" aria-label="Previous headline">
                                <span class="text-xl leading-none">&#8249;</span>
                            </button>
                            <button type="button" x-on:click="nextSlide()" class="absolute right-3 top-1/2 z-20 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-black/55 text-white backdrop-blur-sm transition hover:bg-black/75" aria-label="Next headline">
                                <span class="text-xl leading-none">&#8250;</span>
                            </button>
                        @endif
                    </div>

                    @if ($headlinePosts->count() > 1)
                        <div class="grid grid-cols-1 border-t border-white/10 bg-[var(--color-secondary-light)] sm:grid-cols-2 xl:grid-cols-5">
                            @foreach ($headlinePosts as $post)
                                <button
                                    type="button"
                                    x-on:click="currentSlide = {{ $loop->index }}"
                                    class="border-b border-white/10 px-4 py-4 text-left transition sm:border-r xl:border-b-0"
                                    x-bind:class="currentSlide === {{ $loop->index }} ? 'bg-white/10' : 'hover:bg-white/5'"
                                >
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-[var(--color-primary)]">{{ $post->category?->name ?? 'News' }}</div>
                                    <div class="mt-1 line-clamp-2 text-sm font-semibold leading-snug text-white">{{ $post->title }}</div>
                                    <div class="mt-2 text-[11px] text-gray-300">{{ $post->published_at?->translatedFormat('d M Y') ?? '-' }}</div>
                                </button>
                            @endforeach
                        </div>
                    @endif
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

                @if (! $search && $videoPosts->isNotEmpty())
                    <section class="mb-10 overflow-hidden rounded-2xl bg-[var(--color-secondary)] px-5 py-6 text-white sm:px-6">
                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="flex items-center gap-2 text-xl font-black text-white">
                                <span class="h-6 w-1 rounded-full bg-[var(--color-primary)]"></span>
                                Video Pilihan
                            </h2>
                            <span class="content-type-badge content-type-badge-on-dark">Video</span>
                        </div>

                        <div class="space-y-4">
                            <div>
                                @include('blog.partials.post-card', ['post' => $videoPosts->first(), 'featured' => true])
                            </div>

                            @if ($videoPosts->count() > 1)
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                    @foreach ($videoPosts->slice(1, 3) as $post)
                                        <article class="rounded-xl border border-white/10 bg-white/5 p-3">
                                            @include('blog.partials.post-card', ['post' => $post, 'compact' => true])
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </section>
                @endif

                @foreach ($categorySections as $section)
                    <section class="mb-10">
                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="flex items-center gap-2 text-xl font-black text-[var(--color-secondary)]">
                                <span class="h-6 w-1 rounded-full bg-[var(--color-primary)]"></span>
                                {{ $section['category']->name }}
                            </h2>
                            <a href="{{ $section['category']->publicUrl() }}" class="text-sm font-semibold text-[var(--color-primary)] hover:underline">Lihat Semua -&gt;</a>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach ($section['posts'] as $post)
                                @include('blog.partials.post-card', ['post' => $post, 'compact' => true])
                            @endforeach
                        </div>
                    </section>
                @endforeach

                @if (! $search && $galleryPosts->isNotEmpty())
                    <section class="mb-10">
                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="flex items-center gap-2 text-xl font-black text-[var(--color-secondary)]">
                                <span class="h-6 w-1 rounded-full bg-[var(--color-primary)]"></span>
                                Galeri Foto
                            </h2>
                            <span class="content-type-badge">Gallery</span>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach ($galleryPosts as $post)
                                <div class="{{ $loop->first ? 'sm:col-span-2' : '' }}">
                                    @include('blog.partials.post-card', ['post' => $post, 'featured' => $loop->first])
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            @include('blog.partials.sidebar', ['latestPosts' => $latestPosts, 'popularPosts' => $latestPosts, 'infographicPosts' => $infographicPosts])
        </div>
    </main>
</x-public-layout>
