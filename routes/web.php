<?php

use App\Http\Controllers\Admin\AdBannerController as AdminAdBannerController;
use App\Http\Controllers\Admin\AdvertorialController as AdminAdvertorialController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\SiteSettingController as AdminSiteSettingController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

// ──────────────────────────────────────────────
// Public Routes
// ──────────────────────────────────────────────

Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/blog', [PostController::class, 'index'])->name('posts.index');
Route::get('/tag/{tag:slug}', [TagController::class, 'show'])->name('tags.show');

// ──────────────────────────────────────────────
// Admin Routes (auth-protected)
// ──────────────────────────────────────────────

Route::prefix('admin')->middleware(['auth', 'verified'])->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('posts', AdminPostController::class)->except('show');
    Route::resource('advertorials', AdminAdvertorialController::class);
    Route::get('ad-banners', [AdminAdBannerController::class, 'edit'])->name('ad-banners.edit');
    Route::put('ad-banners', [AdminAdBannerController::class, 'update'])->name('ad-banners.update');
    Route::resource('categories', AdminCategoryController::class)->except('show');
    Route::get('site-settings', [AdminSiteSettingController::class, 'edit'])->name('site-settings.edit');
    Route::put('site-settings', [AdminSiteSettingController::class, 'update'])->name('site-settings.update');
    Route::resource('tags', AdminTagController::class)->except('show');
    Route::resource('users', AdminUserController::class)->except('show');

    Route::post('media/upload', [MediaController::class, 'upload'])->name('media.upload');
    Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
});

Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ──────────────────────────────────────────────
// Breeze Profile Routes
// ──────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/{path}', [PostController::class, 'resolvePath'])
    ->where('path', '.*');
