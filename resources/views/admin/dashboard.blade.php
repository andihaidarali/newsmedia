<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Dashboard</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Statistik operasional post, advertorial, dan performa tim redaksi.</p>
            </div>
            <a href="{{ route('admin.posts.create') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                New Post
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Post Bulan Ini</div>
                    <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stats['total_posts_this_month']) }}</div>
                    <div class="mt-3 text-sm text-gray-500">{{ now()->translatedFormat('F Y') }}</div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Drafts</div>
                    <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stats['draft_posts']) }}</div>
                    <div class="mt-3 text-sm text-gray-500">Semua post dengan status draft</div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Advertorial Aktif</div>
                    <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stats['active_advertorials']) }}</div>
                    <div class="mt-3 text-sm text-gray-500">Berdasarkan periode kerjasama hari ini</div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Users</div>
                    <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stats['total_users']) }}</div>
                    <div class="mt-3 text-sm text-gray-500">Total akun aktif di sistem</div>
                </div>
            </div>

            <section class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex flex-col gap-4 border-b border-gray-200 px-5 py-4 dark:border-gray-700 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Filter Produksi Bulanan</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Filter ini dipakai untuk tabel editor dan reporter.</p>
                    </div>

                    <form method="GET" action="{{ route('admin.dashboard') }}" class="grid gap-3 sm:grid-cols-[12rem_12rem_auto]">
                        <div>
                            <x-input-label for="month" value="Month" />
                            <select id="month" name="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                @foreach (range(1, 12) as $month)
                                    <option value="{{ $month }}" @selected((int) $filters['month'] === $month)>{{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="year" value="Year" />
                            <select id="year" name="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                @forelse ($years as $year)
                                    <option value="{{ $year }}" @selected((string) $filters['year'] === (string) $year)>{{ $year }}</option>
                                @empty
                                    <option value="{{ now()->year }}">{{ now()->year }}</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="flex items-end gap-3">
                            <x-primary-button>Filter</x-primary-button>
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Reset</a>
                        </div>
                    </form>
                </div>
            </section>

            <div class="grid gap-6 xl:grid-cols-2">
                <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Grafik Editor 12 Bulan</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Jumlah post per editor untuk 12 bulan terakhir.</p>
                    </div>
                    <div class="h-80 px-5 py-5">
                        <canvas
                            data-line-chart
                            data-chart='@json($editorChart)'
                            aria-label="Editor performance chart"
                        ></canvas>
                    </div>
                </section>

                <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Grafik Reporter 12 Bulan</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Jumlah post per reporter untuk 12 bulan terakhir.</p>
                    </div>
                    <div class="h-80 px-5 py-5">
                        <canvas
                            data-line-chart
                            data-chart='@json($reporterChart)'
                            aria-label="Reporter performance chart"
                        ></canvas>
                    </div>
                </section>
            </div>

            <div class="grid gap-6 xl:grid-cols-2">
                <section class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Tabel Editor</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Jumlah post editor untuk {{ \Carbon\Carbon::create()->month($filters['month'])->translatedFormat('F') }} {{ $filters['year'] }}.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Editor</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Email</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Jumlah Post</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($editorRows as $user)
                                    <tr>
                                        <td class="px-5 py-4">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                        </td>
                                        <td class="px-5 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                                        <td class="px-5 py-4 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">{{ number_format($user->posts_count) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-5 py-8 text-center text-sm text-gray-500">No editor data found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Tabel Reporter</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Jumlah post reporter untuk {{ \Carbon\Carbon::create()->month($filters['month'])->translatedFormat('F') }} {{ $filters['year'] }}.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Reporter</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Email</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Jumlah Post</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($reporterRows as $user)
                                    <tr>
                                        <td class="px-5 py-4">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                        </td>
                                        <td class="px-5 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                                        <td class="px-5 py-4 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">{{ number_format($user->posts_count) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-5 py-8 text-center text-sm text-gray-500">No reporter data found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
