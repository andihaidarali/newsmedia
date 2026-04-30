<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSiteSettingRequest;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        $siteSetting = SiteSetting::current();

        Gate::authorize('view', $siteSetting);

        return view('admin.site-settings.edit', compact('siteSetting'));
    }

    public function update(UpdateSiteSettingRequest $request): RedirectResponse
    {
        $siteSetting = SiteSetting::current();

        Gate::authorize('update', $siteSetting);

        $data = $request->validated();

        foreach ([
            'site_logo' => 'site-settings/logo',
            'site_favicon' => 'site-settings/favicon',
            'default_featured_image' => 'site-settings/default-featured',
        ] as $field => $directory) {
            if (! $request->hasFile($field)) {
                unset($data[$field]);

                continue;
            }

            if ($siteSetting->{$field}) {
                Storage::disk('public')->delete($siteSetting->{$field});
            }

            $data[$field] = $request->file($field)->store($directory, 'public');
        }

        $siteSetting->update($data);

        return redirect()
            ->route('admin.site-settings.edit')
            ->with('success', 'Site settings updated successfully.');
    }
}
