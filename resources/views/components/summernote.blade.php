<div {{ $attributes->merge(['class' => 'summernote-container border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm bg-white']) }}>
    <textarea x-ref="summernote" x-data x-init="() => initSummernote($refs.summernote)" {{ $attributes->merge(['id' => $id]) }}>{{ $value ?? $slot }}</textarea>
</div>
