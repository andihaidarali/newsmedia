<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $currentSiteSetting?->site_description ?: config('app.name', 'Laravel') }}">

        <title>{{ $currentSiteSetting?->site_title ?: config('app.name', 'Laravel') }}</title>
        @if ($currentSiteSetting?->siteFaviconUrl())
            <link rel="icon" href="{{ $currentSiteSetting->siteFaviconUrl() }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen bg-gray-100 dark:bg-gray-900 lg:flex">
            @include('layouts.navigation')

            <div class="min-w-0 flex-1">
                <!-- Page Heading -->
                @isset($header)
                    <header class="border-b border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-start gap-4 px-4 py-6 sm:px-6 lg:px-8">
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-md border border-gray-200 bg-white p-2 text-gray-600 shadow-sm hover:bg-gray-50 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-gray-100"
                                @click="if (window.innerWidth >= 1024) { sidebarCollapsed = !sidebarCollapsed } else { sidebarOpen = true }"
                            >
                                <span class="sr-only">Toggle sidebar</span>
                                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <div class="min-w-0 flex-1">
                                {{ $header }}
                            </div>
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
