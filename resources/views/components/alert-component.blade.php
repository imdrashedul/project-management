<div
    role="alert"
    x-data="{ show: true }"
    x-show="show"
    x-transition
    x-init="setTimeout(() => show = false, {{ $timeout ?? 6000 }})"
    class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 mb-4">
    <div {{ $attributes->merge(['class' => 'bg-purple-50 border border-purple-300 text-purple-700 px-4 py-3 rounded']) }}>
        {{ $value ?? $slot }}
    </div>
</div>
