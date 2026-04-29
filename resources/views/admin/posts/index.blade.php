<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Posts</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage articles, publishing status, categories, and tags.</p>
            </div>
            @can('create', App\Models\Post::class)
                <a href="{{ route('admin.posts.create') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    New Post
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <form method="GET" action="{{ route('admin.posts.index') }}" class="mb-6 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="grid gap-4 md:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)_minmax(0,1fr)_auto]">
                    <div>
                        <x-input-label for="advertorial_id" value="Advertorial" />
                        <select id="advertorial_id" name="advertorial_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">All advertorials</option>
                            @foreach ($advertorials as $advertorial)
                                <option value="{{ $advertorial->id }}" @selected((string) ($filters['advertorial_id'] ?? '') === (string) $advertorial->id)>
                                    {{ $advertorial->name }} - {{ $advertorial->partner }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="month" value="Month" />
                        <select id="month" name="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">All months</option>
                            @foreach (range(1, 12) as $month)
                                <option value="{{ $month }}" @selected((string) ($filters['month'] ?? '') === (string) $month)>{{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="year" value="Year" />
                        <select id="year" name="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">All years</option>
                            @foreach ($years as $year)
                                <option value="{{ $year }}" @selected((string) ($filters['year'] ?? '') === (string) $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-3">
                        <x-primary-button>Filter</x-primary-button>
                        <a href="{{ route('admin.posts.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Reset</a>
                    </div>
                </div>
            </form>

            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Post</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Author</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Tags</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Published</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($posts as $post)
                                <tr>
                                    <td class="px-5 py-4">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $post->title }}</div>
                                        <div class="mt-1 text-sm text-gray-500">
                                            {{ $post->category?->name ?? 'Uncategorized' }} / {{ $post->slug }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span @class([
                                            'rounded-full px-2.5 py-1 text-xs font-medium capitalize',
                                            'bg-emerald-100 text-emerald-800' => $post->status === 'published',
                                            'bg-amber-100 text-amber-800' => $post->status === 'scheduled',
                                            'bg-gray-100 text-gray-700' => $post->status === 'draft',
                                            'bg-red-100 text-red-800' => $post->status === 'archived',
                                        ])>{{ $post->status }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        @if ($post->writerCredits->isNotEmpty())
                                            {{ $post->writerCredits->pluck('name')->join(', ') }}
                                        @else
                                            {{ $post->author?->name ?? 'Unknown' }}
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex max-w-xs flex-wrap gap-1.5">
                                            @forelse ($post->tags as $tag)
                                                <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-700">{{ $tag->name }}</span>
                                            @empty
                                                <span class="text-sm text-gray-500">No tags</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-500">
                                        {{ $post->published_at?->format('M d, Y H:i') ?? 'Not set' }}
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="flex justify-end gap-3">
                                            @can('update', $post)
                                                <a href="{{ route('admin.posts.edit', $post) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Edit</a>
                                            @endcan
                                            @can('delete', $post)
                                                <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Delete this post?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">Delete</button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-sm text-gray-500">No posts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-200 px-5 py-4 dark:border-gray-700">
                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
