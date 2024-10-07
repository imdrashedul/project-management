<x-alert-component {{ $attributes->merge(['class' => '!bg-red-100 !border-red-400 !text-red-700']) }}>
    {{ $value ?? $slot }}
</x-alert-component>
