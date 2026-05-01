@props([
    'categories',
    'depth' => 0,
    'mobile' => false,
])

@foreach ($categories as $category)
    @php
        $isActive = request()->path() === trim($category->slugPath(), '/')
            || str_starts_with(request()->path(), trim($category->slugPath(), '/').'/');
    @endphp

    @if ($mobile)
        <div class="space-y-1">
            <a href="{{ $category->publicUrl() }}" class="block py-2 text-sm font-medium {{ $isActive ? 'text-[var(--color-primary)]' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-primary)]' }}" style="padding-left: {{ ($depth * 16) + 12 }}px;">
                {{ $category->name }}
            </a>
            @if ($category->children->isNotEmpty())
                @include('blog.partials.category-nav-children', ['categories' => $category->children, 'depth' => $depth + 1, 'mobile' => true])
            @endif
        </div>
    @else
        <div class="space-y-2">
            <a href="{{ $category->publicUrl() }}" class="block text-sm font-semibold transition-colors {{ $isActive ? 'text-[var(--color-primary)]' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-primary)]' }}">
                {{ str_repeat('- ', $depth) }}{{ $category->name }}
            </a>
            @if ($category->children->isNotEmpty())
                <div class="space-y-2 pl-3">
                    @include('blog.partials.category-nav-children', ['categories' => $category->children, 'depth' => $depth + 1, 'mobile' => false])
                </div>
            @endif
        </div>
    @endif
@endforeach
