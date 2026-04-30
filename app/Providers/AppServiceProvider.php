<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\SiteSetting;
use App\Models\Tag;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $siteSetting = null;

            if (Schema::hasTable('site_settings')) {
                $siteSetting = SiteSetting::query()->first();
            }

            $view->with('currentSiteSetting', $siteSetting);
        });

        View::composer('blog.*', function ($view) {
            $navigationCategories = collect();
            $popularTags = collect();

            if (Schema::hasTable('categories')) {
                $navigationCategories = Category::query()
                    ->whereNull('parent_id')
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->limit(12)
                    ->get();
            }

            if (Schema::hasTable('tags') && Schema::hasTable('post_tag') && Schema::hasTable('posts')) {
                $popularTags = Tag::query()
                    ->withCount(['posts' => fn ($query) => $query->published()])
                    ->orderByDesc('posts_count')
                    ->orderBy('name')
                    ->limit(10)
                    ->get();
            }

            $view->with([
                'navigationCategories' => $navigationCategories,
                'popularTags' => $popularTags,
            ]);
        });
    }
}
