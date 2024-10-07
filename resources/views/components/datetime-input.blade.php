<div x-data="{ datetime: '{{ $value ?? $slot }}' }" x-init="initFlatpickr($refs.datetime)" class="relative">
    <input
        type="text"
        x-ref="datetime"
        x-model="datetime"
        {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm', 'placeholder' => 'Enter date and time']) }} />
</div>
