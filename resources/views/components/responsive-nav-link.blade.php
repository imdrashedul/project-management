@props(['active'])

@php
    $classes = $active ?? false ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-purple-400 text-start text-base font-medium text-purple-700 bg-purple-200 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition duration-150 ease-in-out' : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-purple-200 hover:text-purple-700 hover:bg-purple-200 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
