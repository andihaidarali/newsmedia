<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Site Settings</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configure the default branding and metadata for the site.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.site-settings.update') }}" enctype="multipart/form-data" class="space-y-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                @csrf
                @method('PUT')

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <x-input-label for="site_title" value="Site Title" />
                        <x-text-input id="site_title" name="site_title" type="text" class="mt-1 block w-full" :value="old('site_title', $siteSetting->site_title)" />
                        <x-input-error :messages="$errors->get('site_title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="subtitle" value="Subtitle" />
                        <x-text-input id="subtitle" name="subtitle" type="text" class="mt-1 block w-full" :value="old('subtitle', $siteSetting->subtitle)" />
                        <x-input-error :messages="$errors->get('subtitle')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="site_description" value="Site Description" />
                    <textarea id="site_description" name="site_description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('site_description', $siteSetting->site_description) }}</textarea>
                    <x-input-error :messages="$errors->get('site_description')" class="mt-2" />
                </div>

                <div class="grid gap-6 lg:grid-cols-3">
                    <div>
                        <x-input-label for="site_logo" value="Site Logo" />
                        @if ($siteSetting->siteLogoUrl())
                            <img src="{{ $siteSetting->siteLogoUrl() }}" alt="Site logo" class="mt-2 h-20 w-auto rounded border border-gray-200 bg-gray-50 p-2">
                        @endif
                        <input id="site_logo" name="site_logo" type="file" accept="image/*" class="mt-2 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-200">
                        <x-input-error :messages="$errors->get('site_logo')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="site_favicon" value="Site Favicon" />
                        @if ($siteSetting->siteFaviconUrl())
                            <img src="{{ $siteSetting->siteFaviconUrl() }}" alt="Site favicon" class="mt-2 h-16 w-16 rounded border border-gray-200 bg-gray-50 p-2">
                        @endif
                        <input id="site_favicon" name="site_favicon" type="file" accept="image/*" class="mt-2 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-200">
                        <x-input-error :messages="$errors->get('site_favicon')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="default_featured_image" value="Default Featured Image" />
                        @if ($siteSetting->defaultFeaturedImageUrl())
                            <img src="{{ $siteSetting->defaultFeaturedImageUrl() }}" alt="Default featured image" class="mt-2 h-24 w-full rounded border border-gray-200 object-cover">
                        @endif
                        <input id="default_featured_image" name="default_featured_image" type="file" accept="image/*" class="mt-2 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-200">
                        <x-input-error :messages="$errors->get('default_featured_image')" class="mt-2" />
                    </div>
                </div>

                <div class="flex justify-end">
                    <x-primary-button>Save Settings</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
