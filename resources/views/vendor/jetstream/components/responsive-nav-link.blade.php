@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block pl-3 pr-4 py-2 border-l-4 border-secondary text-base font-medium text-zinc-700 bg-zinc-50 focus:outline-none focus:text-zinc-800 focus:bg-zinc-100 transition'
            : 'block pl-3 pr-4 py-2 border-l-4 border-zinc-300 text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
