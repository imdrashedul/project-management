@props(['active'])

@php
    $classes = $active ?? false ? 'inline-flex items-center px-1 pt-1 border-b-[3px] border-purple-100 text-sm font-medium leading-5 text-purple-100 focus:outline-none focus:border-purple-300 transition duration-150 ease-in-out' : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-purple-300 hover:text-purple-200 hover:border-purple-200 focus:outline-none focus:text-purple-200 focus:border-purple-200 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
