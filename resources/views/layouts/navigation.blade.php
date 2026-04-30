<nav
    class="lg:shrink-0"
    :class="sidebarCollapsed ? 'lg:w-24' : 'lg:w-72'"
>
    <div class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 dark:border-gray-700 dark:bg-gray-800 lg:hidden">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            @if ($currentSiteSetting?->siteLogoUrl())
                <img src="{{ $currentSiteSetting->siteLogoUrl() }}" alt="{{ $currentSiteSetting?->site_title ?: config('app.name', 'Laravel') }}" class="block h-9 w-auto">
            @else
                <x-application-logo class="block h-8 w-auto fill-current text-gray-800 dark:text-gray-200" />
            @endif
            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                {{ $currentSiteSetting?->site_title ?: config('app.name', 'Laravel') }}
            </span>
        </a>

        <button @click="sidebarOpen = true" class="inline-flex items-center justify-center rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <div class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden" x-show="sidebarOpen" x-cloak></div>

    <aside
        class="fixed inset-y-0 left-0 z-50 flex h-screen w-72 -translate-x-full flex-col border-r border-gray-200 bg-white transition-all duration-200 dark:border-gray-700 dark:bg-gray-900 lg:sticky lg:top-0 lg:translate-x-0"
        :class="{
            'translate-x-0': sidebarOpen,
            'lg:w-24': sidebarCollapsed,
            'lg:w-72': !sidebarCollapsed
        }"
    >
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-5 dark:border-gray-700">
            <a href="{{ route('admin.dashboard') }}" class="flex flex-1 flex-col items-start gap-3">
                @if ($currentSiteSetting?->siteLogoUrl())
                    <img src="{{ $currentSiteSetting->siteLogoUrl() }}" alt="{{ $currentSiteSetting?->site_title ?: config('app.name', 'Laravel') }}" class="block h-12 w-auto">
                @else
                    <x-application-logo class="block h-10 w-auto fill-current text-gray-800 dark:text-gray-200" />
                @endif
                <div x-show="!sidebarCollapsed" x-cloak class="w-full">
                    <div class="text-base font-semibold text-gray-900 dark:text-gray-100">
                        {{ $currentSiteSetting?->site_title ?: config('app.name', 'Laravel') }}
                    </div>
                    @if ($currentSiteSetting?->subtitle)
                        <div class="mt-1 text-sm leading-5 text-gray-500 dark:text-gray-400">{{ $currentSiteSetting->subtitle }}</div>
                    @endif
                </div>
            </a>

            <button @click="sidebarOpen = false" class="rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200 lg:hidden">
                <span class="sr-only">Close menu</span>
                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-6">
            <div class="space-y-1">
                <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard', 'dashboard')" x-bind:class="sidebarCollapsed ? 'justify-center px-2' : ''">
                    <span x-show="sidebarCollapsed" x-cloak>
                        <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13h8V3H3v10zm10 8h8V11h-8v10zM3 21h8v-6H3v6zm10-10h8V3h-8v8z" />
                        </svg>
                    </span>
                    <span x-show="!sidebarCollapsed" x-cloak>{{ __('Dashboard') }}</span>
                </x-nav-link>
                <x-nav-link :href="route('admin.posts.index')" :active="request()->routeIs('admin.posts.*')" x-bind:class="sidebarCollapsed ? 'justify-center px-2' : ''">
                    <span x-show="sidebarCollapsed" x-cloak>
                        <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586A1 1 0 0113.293 3.293l4.414 4.414A1 1 0 0118 8.414V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                    <span x-show="!sidebarCollapsed" x-cloak>{{ __('Posts') }}</span>
                </x-nav-link>
                @if (auth()->user()->canAccessAdminAdvertorials())
                    <x-nav-link :href="route('admin.advertorials.index')" :active="request()->routeIs('admin.advertorials.*')" x-bind:class="sidebarCollapsed ? 'justify-center px-2' : ''">
                        <span x-show="sidebarCollapsed" x-cloak>
                            <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" x-cloak>{{ __('Advertorials') }}</span>
                    </x-nav-link>
                @endif
                @if (auth()->user()->canAccessAdminBanners())
                    <x-nav-link :href="route('admin.ad-banners.edit')" :active="request()->routeIs('admin.ad-banners.*')" x-bind:class="sidebarCollapsed ? 'justify-center px-2' : ''">
                        <span x-show="sidebarCollapsed" x-cloak>
                            <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5h16v10H4V5zm0 14h16M8 19v-4m8 4v-4" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" x-cloak>{{ __('Banners') }}</span>
                    </x-nav-link>
                @endif
                <x-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')" x-bind:class="sidebarCollapsed ? 'justify-center px-2' : ''">
                    <span x-show="sidebarCollapsed" x-cloak>
                        <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h7v13H3V7zm11-4h7v17h-7V3z" />
                        </svg>
                    </span>
                    <span x-show="!sidebarCollapsed" x-cloak>{{ __('Categories') }}</span>
                </x-nav-link>
                <x-nav-link :href="route('admin.tags.index')" :active="request()->routeIs('admin.tags.*')" x-bind:class="sidebarCollapsed ? 'justify-center px-2' : ''">
                    <span x-show="sidebarCollapsed" x-cloak>
                        <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M3 11l8.586 8.586a2 2 0 002.828 0l6.172-6.172a2 2 0 000-2.828L12 2H3v9z" />
                        </svg>
                    </span>
                    <span x-show="!sidebarCollapsed" x-cloak>{{ __('Tags') }}</span>
                </x-nav-link>
                @can('viewAny', App\Models\User::class)
                    <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" x-bind:class="sidebarCollapsed ? 'justify-center px-2' : ''">
                        <span x-show="sidebarCollapsed" x-cloak>
                            <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m8-6a4 4 0 11-8 0 4 4 0 018 0zm-10 0a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" x-cloak>{{ __('Users') }}</span>
                    </x-nav-link>
                    <x-nav-link :href="route('admin.site-settings.edit')" :active="request()->routeIs('admin.site-settings.*')" x-bind:class="sidebarCollapsed ? 'justify-center px-2' : ''">
                        <span x-show="sidebarCollapsed" x-cloak>
                            <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317a1 1 0 011.35-.936l1.32.528a1 1 0 00.894 0l1.32-.528a1 1 0 011.35.936l.122 1.428a1 1 0 00.65.86l1.364.512a1 1 0 01.54 1.458l-.73 1.233a1 1 0 000 1.018l.73 1.233a1 1 0 01-.54 1.458l-1.364.512a1 1 0 00-.65.86l-.122 1.428a1 1 0 01-1.35.936l-1.32-.528a1 1 0 00-.894 0l-1.32.528a1 1 0 01-1.35-.936l-.122-1.428a1 1 0 00-.65-.86l-1.364-.512a1 1 0 01-.54-1.458l.73-1.233a1 1 0 000-1.018l-.73-1.233a1 1 0 01.54-1.458l1.364-.512a1 1 0 00.65-.86l.122-1.428z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" x-cloak>{{ __('Settings') }}</span>
                    </x-nav-link>
                @endcan
            </div>
        </div>

        <div class="border-t border-gray-200 px-4 py-4 dark:border-gray-700">
            <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                <div x-show="!sidebarCollapsed" x-cloak>
                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ Auth::user()->name }}</div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                </div>
                <div class="mt-4 space-y-2">
                    <a href="{{ route('profile.edit') }}" class="flex items-center rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:bg-white hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-gray-100">
                        <span x-show="!sidebarCollapsed" x-cloak>{{ __('Profile') }}</span>
                        <span x-show="sidebarCollapsed" x-cloak>P</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center rounded-md px-3 py-2 text-left text-sm font-medium text-red-600 hover:bg-white dark:hover:bg-gray-700">
                            <span x-show="!sidebarCollapsed" x-cloak>{{ __('Log Out') }}</span>
                            <span x-show="sidebarCollapsed" x-cloak>O</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>
</nav>
