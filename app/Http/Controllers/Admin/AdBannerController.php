<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdBannerSettingsRequest;
use App\Models\AdBanner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdBannerController extends Controller
{
    public function edit(): View
    {
        $banners = $this->loadBanners();

        Gate::authorize('viewAny', AdBanner::class);

        return view('admin.ad-banners.edit', compact('banners'));
    }

    public function update(UpdateAdBannerSettingsRequest $request): RedirectResponse
    {
        $banners = $this->loadBanners()->keyBy('slot_number');

        Gate::authorize('viewAny', AdBanner::class);

        foreach (range(1, 12) as $slot) {
            $banner = $banners[$slot];
            Gate::authorize('update', $banner);

            $payload = [
                'name' => data_get($request->validated(), "banners.$slot.name"),
                'description' => data_get($request->validated(), "banners.$slot.description"),
            ];

            if ($request->hasFile("banners.$slot.image")) {
                if ($banner->image_path) {
                    Storage::disk('public')->delete($banner->image_path);
                }

                $payload['image_path'] = $request->file("banners.$slot.image")->store("ad-banners/banner-$slot", 'public');
            }

            $banner->update($payload);
        }

        return redirect()
            ->route('admin.ad-banners.edit')
            ->with('success', 'Banner settings updated successfully.');
    }

    private function loadBanners()
    {
        foreach (range(1, 12) as $slot) {
            AdBanner::query()->firstOrCreate(['slot_number' => $slot]);
        }

        return AdBanner::query()
            ->orderBy('slot_number')
            ->get();
    }
}
