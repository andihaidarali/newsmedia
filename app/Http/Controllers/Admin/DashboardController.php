<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Advertorial;
use App\Models\Post;
use App\Models\PostAuthor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with summary statistics.
     */
    public function index(Request $request)
    {
        $selectedMonth = $request->integer('month') ?: now()->month;
        $selectedYear = $request->integer('year') ?: now()->year;

        $stats = [
            'published_posts_in_period' => Post::query()
                ->whereNotNull('published_at')
                ->whereMonth('published_at', $selectedMonth)
                ->whereYear('published_at', $selectedYear)
                ->count(),
            'draft_posts' => Post::draft()->count(),
            'active_advertorials' => Advertorial::query()
                ->whereDate('starts_at', '<=', now()->toDateString())
                ->whereDate('ends_at', '>=', now()->toDateString())
                ->count(),
            'total_users' => User::query()
                ->where('role', Role::ADMINISTRATOR->value)
                ->orWhere(function ($query) {
                    $query->whereIn('role', [Role::EDITOR->value, Role::REPORTER->value])
                        ->where('is_active', true);
                })
                ->count(),
        ];

        $contributors = User::query()
            ->whereIn('role', [Role::EDITOR->value, Role::REPORTER->value])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        $selectedPeriodCounts = PostAuthor::query()
            ->selectRaw('post_authors.user_id, count(distinct post_authors.post_id) as aggregate')
            ->join('posts', 'posts.id', '=', 'post_authors.post_id')
            ->whereNotNull('post_authors.user_id')
            ->whereNotNull('posts.published_at')
            ->whereMonth('posts.published_at', $selectedMonth)
            ->whereYear('posts.published_at', $selectedYear)
            ->groupBy('post_authors.user_id')
            ->pluck('aggregate', 'post_authors.user_id');

        $contributors = $contributors->map(function (User $user) use ($selectedPeriodCounts) {
            $user->posts_count = (int) ($selectedPeriodCounts[$user->id] ?? 0);

            return $user;
        });

        $editorRows = $contributors
            ->filter(fn (User $user) => $user->role === Role::EDITOR)
            ->values();

        $reporterRows = $contributors
            ->filter(fn (User $user) => $user->role === Role::REPORTER)
            ->values();

        $rangeStart = now()->startOfMonth()->subMonths(11);
        $monthLabels = collect(range(0, 11))
            ->map(fn (int $offset) => $rangeStart->copy()->addMonths($offset))
            ->values();

        $overallMonthlyCounts = Post::query()
            ->selectRaw('strftime("%Y-%m", published_at) as period, count(*) as aggregate')
            ->whereNotNull('published_at')
            ->whereDate('published_at', '>=', $rangeStart->toDateString())
            ->groupBy('period')
            ->pluck('aggregate', 'period');

        $monthlyCounts = PostAuthor::query()
            ->selectRaw('post_authors.user_id, strftime("%Y-%m", posts.published_at) as period, count(distinct post_authors.post_id) as aggregate')
            ->join('posts', 'posts.id', '=', 'post_authors.post_id')
            ->whereNotNull('post_authors.user_id')
            ->whereNotNull('posts.published_at')
            ->whereDate('posts.published_at', '>=', $rangeStart->toDateString())
            ->groupBy('post_authors.user_id', 'period')
            ->get()
            ->groupBy('user_id');

        $buildChartDatasets = function ($users, string $baseColor) use ($monthLabels, $monthlyCounts) {
            return $users->values()->map(function (User $user, int $index) use ($baseColor, $monthLabels, $monthlyCounts) {
                $userCounts = collect($monthlyCounts->get($user->id, []))
                    ->mapWithKeys(fn ($row) => [$row->period => (int) $row->aggregate]);

                return [
                    'label' => $user->name,
                    'data' => $monthLabels
                        ->map(fn (Carbon $month) => $userCounts->get($month->format('Y-m'), 0))
                        ->all(),
                    'borderColor' => sprintf('hsl(%d 70%% 45%%)', ($baseColor + ($index * 37)) % 360),
                    'backgroundColor' => sprintf('hsla(%d 70%% 45%% / 0.12)', ($baseColor + ($index * 37)) % 360),
                    'tension' => 0.3,
                    'fill' => false,
                ];
            })->all();
        };

        $editorChart = [
            'labels' => $monthLabels->map(fn (Carbon $month) => $month->translatedFormat('M Y'))->all(),
            'datasets' => $buildChartDatasets($editorRows, 224),
        ];

        $reporterChart = [
            'labels' => $monthLabels->map(fn (Carbon $month) => $month->translatedFormat('M Y'))->all(),
            'datasets' => $buildChartDatasets($reporterRows, 18),
        ];

        $overallPostsChart = [
            'type' => 'bar',
            'labels' => $monthLabels->map(fn (Carbon $month) => $month->translatedFormat('M Y'))->all(),
            'datasets' => [[
                'label' => 'Total Published Posts',
                'data' => $monthLabels
                    ->map(fn (Carbon $month) => (int) ($overallMonthlyCounts[$month->format('Y-m')] ?? 0))
                    ->all(),
                'borderColor' => 'hsl(198 82% 42%)',
                'backgroundColor' => 'hsla(198 82% 42% / 0.8)',
                'borderWidth' => 1,
                'borderRadius' => 6,
            ]],
        ];

        $years = Post::query()
            ->selectRaw('strftime("%Y", published_at) as year')
            ->whereNotNull('published_at')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->filter()
            ->values();

        return view('admin.dashboard', [
            'filters' => [
                'month' => $selectedMonth,
                'year' => $selectedYear,
            ],
            'filterLabel' => Carbon::create()->month($selectedMonth)->translatedFormat('F').' '.$selectedYear,
            'editorChart' => $editorChart,
            'editorRows' => $editorRows,
            'overallPostsChart' => $overallPostsChart,
            'reporterChart' => $reporterChart,
            'reporterRows' => $reporterRows,
            'stats' => $stats,
            'years' => $years,
        ]);
    }
}
