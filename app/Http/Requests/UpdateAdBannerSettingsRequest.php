<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAdBannerSettingsRequest extends FormRequest
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
        $rules = [];

        foreach (range(1, 12) as $slot) {
            $rules["banners.$slot.name"] = ['nullable', 'string', 'max:255'];
            $rules["banners.$slot.description"] = ['nullable', 'string', 'max:1000'];
            $rules["banners.$slot.image"] = ['nullable', 'image', 'mimes:webp', 'max:1024'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'max' => 'The uploaded banner image must not exceed 1MB.',
            'mimes' => 'The banner image must be a WebP file.',
        ];
    }
}
