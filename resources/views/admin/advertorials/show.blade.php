<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $advertorial->name }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Detail advertorial dan daftar post yang terhubung.</p>
            </div>
            <div class="flex items-center gap-3 print:hidden">
                <a href="{{ route('admin.advertorials.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    Back to advertorials
                </a>
                <button type="button" onclick="window.print()" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    Save as PDF
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Advertorial Information</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr>
                                <th class="w-56 bg-gray-50 px-5 py-4 text-left text-sm font-semibold text-gray-600 dark:bg-gray-900 dark:text-gray-300">Nama Advertorial</th>
                                <td class="px-5 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $advertorial->name }}</td>
                            </tr>
                            <tr>
                                <th class="bg-gray-50 px-5 py-4 text-left text-sm font-semibold text-gray-600 dark:bg-gray-900 dark:text-gray-300">Kerjasama Oleh</th>
                                <td class="px-5 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $advertorial->partner }}</td>
                            </tr>
                            <tr>
                                <th class="bg-gray-50 px-5 py-4 text-left text-sm font-semibold text-gray-600 dark:bg-gray-900 dark:text-gray-300">Awal Kerjasama</th>
                                <td class="px-5 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $advertorial->starts_at?->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th class="bg-gray-50 px-5 py-4 text-left text-sm font-semibold text-gray-600 dark:bg-gray-900 dark:text-gray-300">Akhir Kerjasama</th>
                                <td class="px-5 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $advertorial->ends_at?->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th class="bg-gray-50 px-5 py-4 text-left text-sm font-semibold text-gray-600 dark:bg-gray-900 dark:text-gray-300">Jumlah Post</th>
                                <td class="px-5 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $advertorial->posts->count() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Posts</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Title</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Author</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Publish Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($advertorial->posts as $post)
                                <tr>
                                    <td class="px-5 py-4">
                                        <a href="{{ $post->publicUrl() }}" class="font-medium text-indigo-600 hover:text-indigo-700" target="_blank" rel="noreferrer">
                                            {{ $post->title }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $post->author?->name ?? 'Unknown' }}</td>
                                    <td class="px-5 py-4 text-sm text-gray-500">{{ $post->published_at?->format('d M Y H:i') ?? 'Not published' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-8 text-center text-sm text-gray-500">No connected posts.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
