<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'site_title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'site_description' => ['nullable', 'string', 'max:2000'],
            'site_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg,webp', 'max:2048'],
            'site_favicon' => ['nullable', 'image', 'mimes:ico,png,svg,webp', 'max:1024'],
            'default_featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'social_links' => ['nullable', 'array'],
            'social_links.*.platform' => ['required_with:social_links.*.url', 'nullable', 'in:facebook,instagram,youtube,x,tiktok,linkedin,telegram,whatsapp'],
            'social_links.*.url' => ['required_with:social_links.*.platform', 'nullable', 'url', 'max:2048'],
        ];
    }
}
