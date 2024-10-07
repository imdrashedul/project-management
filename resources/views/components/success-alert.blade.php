<x-alert-component {{ $attributes->merge(['class' => '!bg-green-100 !border-green-400 !text-green-700']) }}>
    {{ $value ?? $slot }}
</x-alert-component>
