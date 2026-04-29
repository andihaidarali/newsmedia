<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'type' => $this->input('type', 'article'),
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'manual_author_names' => ['nullable', 'string', 'max:1000'],
            'type' => ['required', 'in:article,video,gallery,infographic'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'advertorial_id' => ['nullable', 'exists:advertorials,id'],
            'new_category_name' => ['nullable', 'string', 'max:255'],
            'new_category_slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
            'new_category_description' => ['nullable', 'string'],
            'new_advertorial_name' => ['nullable', 'string', 'max:255'],
            'new_advertorial_partner' => ['required_with:new_advertorial_name', 'nullable', 'string', 'max:255'],
            'new_advertorial_starts_at' => ['required_with:new_advertorial_name', 'nullable', 'date'],
            'new_advertorial_ends_at' => ['required_with:new_advertorial_name', 'nullable', 'date', 'after_or_equal:new_advertorial_starts_at'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:255'],
            'new_tags' => ['nullable', 'string', 'max:1000'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
            'youtube_url' => ['required_if:type,video', 'nullable', 'url', 'max:2048'],
            'gallery_images' => ['required_if:type,gallery', 'nullable', 'array'],
            'gallery_images.*' => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'infographic_image' => ['required_if:type,infographic', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'status' => ['required', 'in:draft,published,scheduled,archived'],
            'published_at' => [
                Rule::requiredIf(fn () => $this->input('status') === 'scheduled'),
                'nullable',
                'date',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if ($this->input('status') !== 'scheduled' || blank($value)) {
                        return;
                    }

                    if (now()->gte(Carbon::parse($value))) {
                        $fail('The scheduled date must be in the future.');
                    }
                },
            ],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:300'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'published_at.required_if' => 'A publish date is required when scheduling a post.',
            'featured_image.max' => 'The featured image must not exceed 2MB.',
            'youtube_url.required_if' => 'A YouTube link is required for video posts.',
            'gallery_images.required_if' => 'At least one gallery image is required for gallery posts.',
            'infographic_image.required_if' => 'An infographic image is required for infographic posts.',
            'new_advertorial_partner.required_with' => 'Kerjasama oleh wajib diisi saat membuat advertorial baru.',
            'new_advertorial_starts_at.required_with' => 'Awal kerjasama wajib diisi saat membuat advertorial baru.',
            'new_advertorial_ends_at.required_with' => 'Akhir kerjasama wajib diisi saat membuat advertorial baru.',
            'new_advertorial_ends_at.after_or_equal' => 'Akhir kerjasama harus sama dengan atau setelah awal kerjasama.',
        ];
    }
}
