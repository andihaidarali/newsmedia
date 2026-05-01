@props([
    'title' => null,
    'description' => null,
    'latestPosts' => null,
    'image' => null,
])

@php
    $resolvedTitle = $title ?: ($currentSiteSetting?->site_title ?: config('app.name', 'Laravel'));
    $resolvedDescription = $description ?: ($currentSiteSetting?->site_description ?: 'Berita terbaru dan artikel pilihan.');
    $resolvedImage = $image ?: $currentSiteSetting?->defaultFeaturedImageUrl();
    $layoutNavigationCategories = $navigationCategories ?? (
        \Illuminate\Support\Facades\Schema::hasTable('categories')
            ? \App\Models\Category::query()
                ->with('descendants')
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->limit(12)
                ->get()
            : collect()
    );
    $layoutLatestPosts = collect($latestPosts ?? [])->take(6);
    $brandName = $currentSiteSetting?->site_title ?: config('app.name', 'Laravel');
    $brandSubtitle = $currentSiteSetting?->subtitle ?: 'Portal Berita Digital Terkini';
    $brandDescription = $currentSiteSetting?->site_description ?: 'Portal berita digital terdepan dengan kabar cepat, akurat, dan terpercaya.';
    $currentPath = trim(request()->path(), '/');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $resolvedTitle }}</title>
        <meta name="description" content="{{ $resolvedDescription }}">
        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ $resolvedTitle }}">
        <meta property="og:description" content="{{ $resolvedDescription }}">
        <meta property="og:url" content="{{ request()->fullUrl() }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $resolvedTitle }}">
        <meta name="twitter:description" content="{{ $resolvedDescription }}">
        @if ($resolvedImage)
            <meta property="og:image" content="{{ $resolvedImage }}">
            <meta name="twitter:image" content="{{ $resolvedImage }}">
        @endif
        @if ($currentSiteSetting?->siteFaviconUrl())
            <link rel="icon" href="{{ $currentSiteSetting->siteFaviconUrl() }}">
        @endif
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[var(--color-surface)] text-[var(--color-text-primary)] antialiased">
        <div class="bg-[var(--color-secondary)] text-white text-xs">
            <div class="mx-auto flex h-8 max-w-7xl items-center justify-between px-4">
                <div class="flex items-center gap-4">
                    <span class="opacity-70">{{ now()->translatedFormat('l, d F Y') }}</span>
                    <span class="opacity-30">|</span>
                    <span class="opacity-70">{{ $brandSubtitle }}</span>
                </div>
                <div class="hidden items-center gap-3 md:flex">
                    <span class="opacity-60">Facebook</span>
                    <span class="opacity-60">Instagram</span>
                    <span class="opacity-60">YouTube</span>
                </div>
            </div>
        </div>

        @if ($layoutLatestPosts->isNotEmpty())
            <div class="bg-[var(--color-primary)] text-white">
                <div class="mx-auto flex h-9 max-w-7xl items-center px-4">
                    <span class="mr-3 shrink-0 rounded-sm bg-white px-3 py-1 text-xs font-bold uppercase tracking-wider text-[var(--color-primary)]">Breaking</span>
                    <div class="ticker-wrap">
                        <div class="ticker text-sm">
                            @foreach (range(1, 2) as $loopIndex)
                                @foreach ($layoutLatestPosts as $breakingPost)
                                    <a href="{{ $breakingPost->publicUrl() }}" class="mx-8 hover:underline">{{ $breakingPost->title }}</a>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <header class="sticky top-0 z-50 border-b border-[var(--color-border)] bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4" x-data="{ open: false }">
                <div class="flex h-16 items-center justify-between">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        @if ($currentSiteSetting?->siteLogoUrl())
                            <img src="{{ $currentSiteSetting->siteLogoUrl() }}" alt="{{ $brandName }}" class="h-10 w-auto max-w-48 object-contain">
                        @else
                            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-[var(--color-primary)] text-xl font-black text-white">
                                {{ \Illuminate\Support\Str::substr($brandName, 0, 1) }}
                            </span>
                        @endif
                        <span class="min-w-0">
                            <span class="block truncate text-xl font-black tracking-tight text-[var(--color-secondary)]">{{ $brandName }}</span>
                            <span class="block truncate text-xs text-[var(--color-text-muted)]">{{ $brandSubtitle }}</span>
                        </span>
                    </a>

                    <div class="mx-8 hidden max-w-md flex-1 md:flex">
                        <form action="{{ route('posts.index') }}" method="GET" class="relative w-full">
                            <input name="q" type="search" value="{{ request('q') }}" placeholder="Cari berita..." class="w-full rounded-full border border-transparent bg-[var(--color-surface-dark)] py-2 pl-4 pr-10 text-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] hover:text-[var(--color-primary)]">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </button>
                        </form>
                    </div>

                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="hidden rounded-lg bg-[var(--color-secondary)] px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-[var(--color-accent)] sm:inline-flex">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="hidden px-4 py-2 text-sm font-medium text-[var(--color-text-secondary)] transition-colors hover:text-[var(--color-primary)] sm:inline-flex">Login</a>
                        @endauth
                        <button type="button" class="p-2 text-[var(--color-text-secondary)] md:hidden" x-on:click="open = !open" aria-label="Menu">
                            <svg x-show="!open" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                            <svg x-cloak x-show="open" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>

                <div class="relative hidden md:block">
                    <nav class="flex flex-wrap items-center gap-x-0.5 overflow-visible">
                        <a href="{{ route('home') }}" class="border-b-2 px-2.5 py-3 text-[13px] font-semibold whitespace-nowrap transition-colors lg:px-3 lg:text-sm {{ request()->routeIs('home') ? 'border-[var(--color-primary)] text-[var(--color-primary)]' : 'border-transparent text-[var(--color-text-secondary)] hover:border-[var(--color-primary)] hover:text-[var(--color-primary)]' }}">Beranda</a>
                        <a href="{{ route('posts.index') }}" class="border-b-2 px-2.5 py-3 text-[13px] font-semibold whitespace-nowrap transition-colors lg:px-3 lg:text-sm {{ request()->routeIs('posts.index') ? 'border-[var(--color-primary)] text-[var(--color-primary)]' : 'border-transparent text-[var(--color-text-secondary)] hover:border-[var(--color-primary)] hover:text-[var(--color-primary)]' }}">Semua Berita</a>
                        @foreach ($layoutNavigationCategories as $category)
                            @php
                                $isActive = $currentPath === trim($category->slugPath(), '/')
                                    || str_starts_with($currentPath, trim($category->slugPath(), '/').'/');
                            @endphp
                            <div
                                class="relative shrink-0"
                                x-data="{
                                    submenuOpen: false,
                                    submenuStyle: '',
                                    setPosition() {
                                        const rect = this.$refs.trigger.getBoundingClientRect();
                                        const panelWidth = 288;
                                        const left = Math.min(
                                            Math.max(16, rect.left),
                                            window.innerWidth - panelWidth - 16,
                                        );

                                        this.submenuStyle = `top:${Math.round(rect.bottom + 6)}px;left:${Math.round(left)}px;width:${panelWidth}px;`;
                                    },
                                    openSubmenu() {
                                        if (window.innerWidth < 768) {
                                            return;
                                        }

                                        this.setPosition();
                                        this.submenuOpen = true;
                                    },
                                    closeSubmenu() {
                                        this.submenuOpen = false;
                                    },
                                }"
                                x-on:mouseenter="openSubmenu()"
                                x-on:mouseleave="closeSubmenu()"
                                x-on:resize.window="if (submenuOpen) setPosition()"
                                x-on:scroll.window="if (submenuOpen) setPosition()"
                            >
                                <a x-ref="trigger" href="{{ $category->publicUrl() }}" class="border-b-2 px-2.5 py-3 text-[13px] font-semibold whitespace-nowrap transition-colors lg:px-3 lg:text-sm {{ $isActive ? 'border-[var(--color-primary)] text-[var(--color-primary)]' : 'border-transparent text-[var(--color-text-secondary)] hover:border-[var(--color-primary)] hover:text-[var(--color-primary)]' }}">
                                    {{ $category->name }}
                                </a>
                                @if ($category->children->isNotEmpty())
                                    <div
                                        x-cloak
                                        x-show="submenuOpen"
                                        x-bind:style="submenuStyle"
                                        x-transition:enter="transition ease-out duration-150"
                                        x-transition:enter-start="opacity-0 -translate-y-1"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        x-transition:leave="transition ease-in duration-100"
                                        x-transition:leave-start="opacity-100 translate-y-0"
                                        x-transition:leave-end="opacity-0 -translate-y-1"
                                        class="fixed z-[90]"
                                    >
                                        <div class="rounded-xl border border-[var(--color-border)] bg-white p-4 shadow-xl">
                                            @include('blog.partials.category-nav-children', ['categories' => $category->children, 'depth' => 0, 'mobile' => false])
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </nav>
                </div>

                <div x-cloak x-show="open" class="border-t border-[var(--color-border)] py-3 md:hidden">
                    <form action="{{ route('posts.index') }}" method="GET" class="mb-3">
                        <input name="q" type="search" value="{{ request('q') }}" placeholder="Cari berita..." class="w-full rounded-lg border-[var(--color-border)] bg-[var(--color-surface-dark)] text-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                    </form>
                    <div class="grid gap-1">
                        <a href="{{ route('home') }}" class="py-2 text-sm font-medium">Beranda</a>
                        <a href="{{ route('posts.index') }}" class="py-2 text-sm font-medium">Semua Berita</a>
                        @foreach ($layoutNavigationCategories as $category)
                            <a href="{{ $category->publicUrl() }}" class="py-2 text-sm font-medium text-[var(--color-text-secondary)] hover:text-[var(--color-primary)]">{{ $category->name }}</a>
                            @if ($category->children->isNotEmpty())
                                @include('blog.partials.category-nav-children', ['categories' => $category->children, 'depth' => 1, 'mobile' => true])
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </header>

        <main class="min-h-[40vh]">
            {{ $slot }}
        </main>

        <footer class="mt-12 bg-[var(--color-secondary)] text-white">
            <div class="mx-auto max-w-7xl px-4 py-12">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                    <div>
                        <a href="{{ route('home') }}" class="mb-4 flex items-center gap-2">
                            @if ($currentSiteSetting?->siteLogoUrl())
                                <img src="{{ $currentSiteSetting->siteLogoUrl() }}" alt="{{ $brandName }}" class="h-8 w-auto max-w-40 object-contain brightness-0 invert">
                            @else
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[var(--color-primary)] text-lg font-black text-white">
                                    {{ \Illuminate\Support\Str::substr($brandName, 0, 1) }}
                                </span>
                            @endif
                            <span class="text-lg font-black">{{ $brandName }}</span>
                        </a>
                        <p class="text-sm leading-relaxed text-gray-400">{{ $brandDescription }}</p>
                    </div>

                    <div>
                        <h4 class="mb-4 text-sm font-bold uppercase tracking-wider text-[var(--color-primary)]">Kategori</h4>
                        <div class="grid grid-cols-2 gap-1">
                            @foreach ($layoutNavigationCategories->take(8) as $category)
                                <a href="{{ $category->publicUrl() }}" class="py-0.5 text-sm text-gray-400 transition-colors hover:text-white">{{ $category->name }}</a>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h4 class="mb-4 text-sm font-bold uppercase tracking-wider text-[var(--color-primary)]">Menu</h4>
                        <a href="{{ route('home') }}" class="block py-0.5 text-sm text-gray-400 transition-colors hover:text-white">Beranda</a>
                        <a href="{{ route('posts.index') }}" class="block py-0.5 text-sm text-gray-400 transition-colors hover:text-white">Semua Berita</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="block py-0.5 text-sm text-gray-400 transition-colors hover:text-white">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="block py-0.5 text-sm text-gray-400 transition-colors hover:text-white">Login</a>
                        @endauth
                    </div>

                    <div>
                        <h4 class="mb-4 text-sm font-bold uppercase tracking-wider text-[var(--color-primary)]">Kontak</h4>
                        <p class="mb-1 text-sm text-gray-400">Email: redaksi@example.com</p>
                        <p class="mb-3 text-sm text-gray-400">{{ $brandSubtitle }}</p>
                        <div class="flex items-center gap-3 text-sm text-gray-400">
                            <span>Facebook</span>
                            <span>Instagram</span>
                            <span>YouTube</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-8 md:flex-row">
                    <p class="text-xs text-gray-500">&copy; {{ now()->year }} {{ $brandName }}. All rights reserved.</p>
                    <p class="text-xs text-gray-500">Member of <strong class="text-gray-400">Dewan Pers Indonesia</strong></p>
                </div>
            </div>
        </footer>
    </body>
</html>
