@props([
    'category',
    'depth' => 0,
    'selectedCategoryId' => null,
])

@php
    $prefix = str_repeat('-', $depth + 1).' ';
@endphp

<option value="{{ $category->id }}" @selected((string) $selectedCategoryId === (string) $category->id)>
    {{ $prefix }}{{ $category->name }}
</option>

@foreach ($category->children as $childCategory)
    @include('admin.posts.partials.category-option', [
        'category' => $childCategory,
        'depth' => $depth + 1,
        'selectedCategoryId' => $selectedCategoryId,
    ])
@endforeach
