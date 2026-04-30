@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center rounded-md bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200'
            : 'flex items-center rounded-md px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-gray-100';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
