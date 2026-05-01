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

            @php
                $socialLinkOptions = [
                    ['value' => 'facebook', 'label' => 'Facebook'],
                    ['value' => 'instagram', 'label' => 'Instagram'],
                    ['value' => 'youtube', 'label' => 'YouTube'],
                    ['value' => 'x', 'label' => 'X'],
                    ['value' => 'tiktok', 'label' => 'TikTok'],
                    ['value' => 'linkedin', 'label' => 'LinkedIn'],
                    ['value' => 'telegram', 'label' => 'Telegram'],
                    ['value' => 'whatsapp', 'label' => 'WhatsApp'],
                ];
                $initialSocialLinks = old('social_links', $siteSetting->social_links ?? []);
            @endphp

            <form method="POST" action="{{ route('admin.site-settings.update') }}" enctype="multipart/form-data" class="space-y-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800" x-data="{ socialLinks: {{ \Illuminate\Support\Js::from($initialSocialLinks) }} }">
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

                <div class="rounded-lg border border-gray-200 p-5 dark:border-gray-700">
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Social Media</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add as many social media profile links as needed.</p>
                        </div>
                        <button
                            type="button"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700"
                            x-on:click="socialLinks.push({ platform: '', url: '' })"
                        >
                            Add Social Media
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(socialLink, index) in socialLinks" :key="index">
                            <div class="grid gap-4 rounded-lg border border-gray-200 p-4 dark:border-gray-700 md:grid-cols-[14rem_1fr_auto]">
                                <div>
                                    <x-input-label ::for="`social_links_${index}_platform`" value="Platform" />
                                    <select
                                        :id="`social_links_${index}_platform`"
                                        x-model="socialLink.platform"
                                        :name="`social_links[${index}][platform]`"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                    >
                                        <option value="">Choose platform</option>
                                        @foreach ($socialLinkOptions as $option)
                                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <x-input-label ::for="`social_links_${index}_url`" value="Profile Link" />
                                    <x-text-input
                                        ::id="`social_links_${index}_url`"
                                        ::name="`social_links[${index}][url]`"
                                        type="url"
                                        class="mt-1 block w-full"
                                        x-model="socialLink.url"
                                        placeholder="https://..."
                                    />
                                </div>

                                <div class="flex items-end">
                                    <button
                                        type="button"
                                        class="inline-flex items-center rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50"
                                        x-on:click="socialLinks.splice(index, 1)"
                                    >
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div x-show="socialLinks.length === 0" class="rounded-md border border-dashed border-gray-300 px-4 py-6 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                            No social media links added yet.
                        </div>
                    </div>

                    <x-input-error :messages="$errors->get('social_links')" class="mt-4" />
                    <x-input-error :messages="$errors->get('social_links.*.platform')" class="mt-2" />
                    <x-input-error :messages="$errors->get('social_links.*.url')" class="mt-2" />
                </div>

                <div class="flex justify-end">
                    <x-primary-button>Save Settings</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
