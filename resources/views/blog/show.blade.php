@php
    $resolvedPostImage = match ($post->type) {
        'video' => (function () use ($post) {
            if (! filled($post->youtube_url)) {
                return null;
            }

            preg_match('/(?:youtu\.be\/|youtube\.com\/watch\?v=)([\w-]+)/', $post->youtube_url, $matches);
            $videoId = $matches[1] ?? null;

            return $videoId ? "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg" : null;
        })(),
        'infographic' => $post->infographic_image ? \Illuminate\Support\Facades\Storage::disk('public')->url($post->infographic_image) : null,
        default => $post->featured_image_url,
    };
@endphp

<x-public-layout :title="$post->meta_title ?: $post->title" :description="$post->meta_description ?: $post->readable_excerpt" :latest-posts="$latestPosts" :image="$resolvedPostImage">
    @php
        $writerNames = $post->writerCredits->pluck('name')->filter()->unique()->values();
    @endphp

    <main class="mx-auto max-w-7xl px-4 py-6">
        <nav class="mb-6 flex items-center text-sm text-[var(--color-text-muted)]">
            <a href="{{ route('home') }}" class="hover:text-[var(--color-primary)] transition-colors">Beranda</a>
            @if ($post->category)
                @foreach ($post->category->ancestorsAndSelf() as $breadcrumbCategory)
                    <span class="mx-2 breadcrumb-separator"></span>
                    <a href="{{ $breadcrumbCategory->publicUrl() }}" class="hover:text-[var(--color-primary)] transition-colors">{{ $breadcrumbCategory->name }}</a>
                @endforeach
            @endif
            <span class="mx-2 breadcrumb-separator"></span>
            <span class="line-clamp-1 text-[var(--color-text-secondary)]">{{ $post->title }}</span>
        </nav>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
            <article class="lg:col-span-8">
                <header class="mb-6">
                    <div class="mb-3 flex items-center gap-2">
                        @if ($post->category)
                            <span class="category-badge">{{ $post->category->name }}</span>
                        @endif
                        @if ($post->advertorial_id)
                            <span class="sponsored-label">Advertorial</span>
                        @endif
                        @if ($post->type === 'video')
                            <span class="rounded-sm bg-red-600 px-2 py-1 text-[10px] font-bold uppercase text-white">Video</span>
                        @endif
                    </div>

                    <h1 class="text-3xl font-black leading-tight text-[var(--color-secondary)] md:text-4xl">
                        {{ $post->title }}
                    </h1>

                    <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-[var(--color-text-secondary)]">
                        <div class="flex items-center gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[var(--color-primary)] text-xs font-bold text-white">
                                {{ \Illuminate\Support\Str::substr($writerNames->first() ?? ($post->author?->name ?? 'U'), 0, 1) }}
                            </div>
                            <div>
                                <span class="font-semibold text-[var(--color-text-primary)]">{{ $writerNames->isNotEmpty() ? $writerNames->join(', ') : ($post->author?->name ?? 'Unknown') }}</span>
                            </div>
                        </div>
                        <span>{{ $post->published_at?->translatedFormat('l, d F Y - H:i') }}</span>
                    </div>
                </header>

                <div class="mb-6 flex items-center gap-2 border-b border-[var(--color-border)] pb-6">
                    <span class="mr-2 text-xs font-semibold uppercase tracking-wider text-[var(--color-text-muted)]">Bagikan:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="share-btn flex h-9 w-9 items-center justify-center rounded-full bg-[#1877F2] text-white">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12S0 5.446 0 12.073c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="https://wa.me/?text={{ urlencode($post->title.' '.request()->fullUrl()) }}" target="_blank" class="share-btn flex h-9 w-9 items-center justify-center rounded-full bg-[#25D366] text-white">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/></svg>
                    </a>
                </div>

                @if ($post->type === 'article' && $post->featured_image_url)
                    <figure class="mb-8">
                        <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="w-full rounded-xl shadow-md">
                        @if ($post->excerpt)
                            <figcaption class="mt-2 text-center text-xs italic text-[var(--color-text-muted)]">{{ $post->excerpt }}</figcaption>
                        @endif
                    </figure>
                @endif

                @if ($post->type === 'video' && $post->youtube_url)
                    @php
                        preg_match('/(?:youtu\.be\/|youtube\.com\/watch\?v=)([\w-]+)/', $post->youtube_url, $matches);
                        $videoId = $matches[1] ?? null;
                    @endphp
                    @if ($videoId)
                        <div class="mb-8 aspect-video overflow-hidden rounded-xl bg-black">
                            <iframe src="https://www.youtube.com/embed/{{ $videoId }}" class="h-full w-full" frameborder="0" allowfullscreen></iframe>
                        </div>
                    @endif
                @endif

                @if ($post->type === 'infographic' && $post->infographic_image)
                    @php
                        $infographicUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($post->infographic_image);
                    @endphp
                    <figure class="mb-8">
                        <img src="{{ $infographicUrl }}" alt="{{ $post->title }}" class="w-full rounded-xl shadow-md">
                        @if ($post->excerpt)
                            <figcaption class="mt-2 text-center text-xs italic text-[var(--color-text-muted)]">{{ $post->excerpt }}</figcaption>
                        @endif
                    </figure>
                @endif

                @if ($post->type === 'gallery' && filled($post->gallery_images))
                    @php
                        $gallerySlides = collect($post->gallery_images)
                            ->map(fn ($image) => \Illuminate\Support\Facades\Storage::disk('public')->url($image))
                            ->values();
                    @endphp
                    <div
                        class="mb-8"
                        x-data="{
                            currentSlide: 0,
                            totalSlides: {{ $gallerySlides->count() }},
                            touchStartX: null,
                            touchEndX: null,
                            previousSlide() {
                                this.currentSlide = this.currentSlide === 0 ? this.totalSlides - 1 : this.currentSlide - 1;
                            },
                            nextSlide() {
                                this.currentSlide = this.currentSlide === this.totalSlides - 1 ? 0 : this.currentSlide + 1;
                            },
                            handleTouchStart(event) {
                                this.touchStartX = event.changedTouches[0]?.clientX ?? null;
                                this.touchEndX = null;
                            },
                            handleTouchEnd(event) {
                                this.touchEndX = event.changedTouches[0]?.clientX ?? null;

                                if (this.touchStartX === null || this.touchEndX === null) {
                                    return;
                                }

                                const swipeDistance = this.touchStartX - this.touchEndX;

                                if (Math.abs(swipeDistance) < 40) {
                                    return;
                                }

                                if (swipeDistance > 0) {
                                    this.nextSlide();

                                    return;
                                }

                                this.previousSlide();
                            },
                        }"
                    >
                        <div class="relative overflow-hidden rounded-xl bg-[var(--color-surface-darker)]" x-on:touchstart.passive="handleTouchStart($event)" x-on:touchend.passive="handleTouchEnd($event)">
                            @foreach ($gallerySlides as $galleryUrl)
                                <div x-show="currentSlide === {{ $loop->index }}" x-transition.opacity class="aspect-[16/10]">
                                    <img src="{{ $galleryUrl }}" alt="{{ $post->title }} - slide {{ $loop->iteration }}" class="h-full w-full object-cover">
                                </div>
                            @endforeach

                            @if ($gallerySlides->count() > 1)
                                <button type="button" x-on:click="previousSlide()" class="absolute left-3 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-black/55 text-white transition hover:bg-black/75" aria-label="Previous image">
                                    <span class="text-lg leading-none">&#8249;</span>
                                </button>
                                <button type="button" x-on:click="nextSlide()" class="absolute right-3 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-black/55 text-white transition hover:bg-black/75" aria-label="Next image">
                                    <span class="text-lg leading-none">&#8250;</span>
                                </button>
                                <div class="absolute bottom-3 left-1/2 z-10 flex -translate-x-1/2 items-center gap-2 rounded-full bg-black/45 px-3 py-1">
                                    @foreach ($gallerySlides as $galleryUrl)
                                        <button type="button" x-on:click="currentSlide = {{ $loop->index }}" class="h-2.5 w-2.5 rounded-full transition" x-bind:class="currentSlide === {{ $loop->index }} ? 'bg-white' : 'bg-white/45'" aria-label="Go to image {{ $loop->iteration }}"></button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @if ($gallerySlides->count() > 1)
                            <div class="mt-3 grid grid-cols-4 gap-2 sm:grid-cols-5">
                                @foreach ($gallerySlides as $galleryUrl)
                                    <button type="button" x-on:click="currentSlide = {{ $loop->index }}" class="overflow-hidden rounded-lg border transition" x-bind:class="currentSlide === {{ $loop->index }} ? 'border-[var(--color-primary)] ring-2 ring-[var(--color-primary)]/20' : 'border-transparent'">
                                        <img src="{{ $galleryUrl }}" alt="{{ $post->title }} thumbnail {{ $loop->iteration }}" class="aspect-square h-full w-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <div class="prose-article">
                    {!! $post->body !!}
                </div>

                @if ($relatedPosts->isNotEmpty())
                    <div class="my-8 rounded-xl border border-[var(--color-border)] bg-[var(--color-surface-dark)] p-5">
                        <h3 class="mb-3 text-sm font-bold uppercase tracking-wider text-[var(--color-secondary)]">Baca Juga</h3>
                        <div class="space-y-2">
                            @foreach ($relatedPosts->take(3) as $relatedPost)
                                <a href="{{ $relatedPost->publicUrl() }}" class="block text-sm font-semibold text-[var(--color-accent)] transition-colors hover:text-[var(--color-primary)]">
                                    -&gt; {{ $relatedPost->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($post->tags->isNotEmpty())
                    <div class="mt-8 flex flex-wrap items-center gap-2 border-b border-[var(--color-border)] pb-6">
                        <span class="text-xs font-bold uppercase tracking-wider text-[var(--color-text-muted)]">Tags:</span>
                        @foreach ($post->tags as $tag)
                            <a href="{{ route('tags.show', $tag) }}" class="rounded-full bg-[var(--color-surface-dark)] px-3 py-1 text-xs font-medium text-[var(--color-text-secondary)] transition-colors hover:bg-[var(--color-primary)] hover:text-white">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                @if ($relatedPosts->isNotEmpty())
                    <section class="mt-10">
                        <h3 class="mb-6 flex items-center gap-2 text-xl font-black text-[var(--color-secondary)]">
                            <span class="h-6 w-1 rounded-full bg-[var(--color-primary)]"></span>
                            Berita Terkait
                        </h3>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach ($relatedPosts as $relatedPost)
                                @include('blog.partials.post-card', ['post' => $relatedPost])
                            @endforeach
                        </div>
                    </section>
                @endif
            </article>

            @include('blog.partials.sidebar', ['latestPosts' => $latestPosts, 'popularPosts' => $relatedPosts])
        </div>
    </main>
</x-public-layout>
