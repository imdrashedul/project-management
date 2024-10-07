@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    {{ $value ?? $slot }}
    @if ($required)
        <i class="fa-solid fa-asterisk text-red-600 text-[0.5rem] align-text-top"></i>
    @endif
</label>
