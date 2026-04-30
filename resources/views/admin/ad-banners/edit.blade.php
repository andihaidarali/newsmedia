<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Banner Settings</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage 12 fixed banner slots for advertising placements.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.ad-banners.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid gap-6 xl:grid-cols-2">
                    @foreach ($banners as $banner)
                        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="mb-5 flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Banner-{{ $banner->slot_number }}</h2>
                                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">Slot {{ $banner->slot_number }}</span>
                            </div>

                            <div class="space-y-5">
                                <div>
                                    <x-input-label for="banners_{{ $banner->slot_number }}_image" value="Gambar Banner (WebP, max 1MB)" />
                                    @if ($banner->imageUrl())
                                        <img src="{{ $banner->imageUrl() }}" alt="Banner {{ $banner->slot_number }}" class="mt-2 h-28 w-full rounded-md border border-gray-200 object-cover">
                                    @endif
                                    <input id="banners_{{ $banner->slot_number }}_image" name="banners[{{ $banner->slot_number }}][image]" type="file" accept=".webp,image/webp" class="mt-2 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-200">
                                    <x-input-error :messages="$errors->get('banners.'.$banner->slot_number.'.image')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="banners_{{ $banner->slot_number }}_name" value="Nama Banner" />
                                    <x-text-input id="banners_{{ $banner->slot_number }}_name" name="banners[{{ $banner->slot_number }}][name]" type="text" class="mt-1 block w-full" :value="old('banners.'.$banner->slot_number.'.name', $banner->name)" />
                                    <x-input-error :messages="$errors->get('banners.'.$banner->slot_number.'.name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="banners_{{ $banner->slot_number }}_description" value="Deskripsi Gambar Banner" />
                                    <textarea id="banners_{{ $banner->slot_number }}_description" name="banners[{{ $banner->slot_number }}][description]" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('banners.'.$banner->slot_number.'.description', $banner->description) }}</textarea>
                                    <x-input-error :messages="$errors->get('banners.'.$banner->slot_number.'.description')" class="mt-2" />
                                </div>
                            </div>
                        </section>
                    @endforeach
                </div>

                <div class="flex justify-end">
                    <x-primary-button>Save Banner Settings</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
