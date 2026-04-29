<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Advertorials</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track sponsored campaigns and the posts attached to them.</p>
            </div>
            @can('create', App\Models\Advertorial::class)
                <a href="{{ route('admin.advertorials.create') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    New Advertorial
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

            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Advertorial</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Partner</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Period</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Posts</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($advertorials as $advertorial)
                                <tr>
                                    <td class="px-5 py-4">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $advertorial->name }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $advertorial->partner }}</td>
                                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        <div>{{ $advertorial->starts_at?->format('d M Y') }}</div>
                                        <div class="text-gray-500">{{ $advertorial->ends_at?->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $advertorial->posts_count }} post{{ $advertorial->posts_count === 1 ? '' : 's' }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('admin.advertorials.show', $advertorial) }}" class="text-sm font-medium text-gray-600 hover:text-gray-700">View</a>
                                            @can('update', $advertorial)
                                                <a href="{{ route('admin.advertorials.edit', $advertorial) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Edit</a>
                                            @endcan
                                            @can('delete', $advertorial)
                                                <form method="POST" action="{{ route('admin.advertorials.destroy', $advertorial) }}" onsubmit="return confirm('Delete this advertorial? Connected posts will lose the advertorial assignment.');">
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
                                    <td colspan="5" class="px-5 py-8 text-center text-sm text-gray-500">No advertorials found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-200 px-5 py-4 dark:border-gray-700">
                    {{ $advertorials->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
