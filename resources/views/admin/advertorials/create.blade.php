<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Create Advertorial</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add a sponsored campaign that can be linked to posts.</p>
            </div>
            <a href="{{ route('admin.advertorials.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Back to advertorials</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.advertorials.store') }}" class="space-y-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                @csrf

                <div>
                    <x-input-label for="name" value="Nama Advertorial" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="partner" value="Kerjasama Oleh" />
                    <x-text-input id="partner" name="partner" type="text" class="mt-1 block w-full" :value="old('partner')" required />
                    <x-input-error :messages="$errors->get('partner')" class="mt-2" />
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <x-input-label for="starts_at" value="Awal Kerjasama" />
                        <x-text-input id="starts_at" name="starts_at" type="date" class="mt-1 block w-full" :value="old('starts_at')" required />
                        <x-input-error :messages="$errors->get('starts_at')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="ends_at" value="Akhir Kerjasama" />
                        <x-text-input id="ends_at" name="ends_at" type="date" class="mt-1 block w-full" :value="old('ends_at')" required />
                        <x-input-error :messages="$errors->get('ends_at')" class="mt-2" />
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.advertorials.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Cancel</a>
                    <x-primary-button>Create Advertorial</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
