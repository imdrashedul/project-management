@props(['api' => '', 'optionApi' => '', 'placeholder' => 'Select an option', 'xref' => null, 'dependency' => null])

<div x-data="{
    selected: '',
    placeholder: '{{ $placeholder }}',
    options: [],
    api: '{{ $api }}',
    optionApi: '{{ $optionApi }}',
    default: '{{ $value ?? '' }}',
    init() {
        initSelect2(this.$refs.{{ $xref ?? 'select' }}, {
            dependency: '{{ $dependency ?? '' }}',
            placeholder: this.placeholder,
            options: this.options,
            api: this.api,
            default: this.default,
            optionApi: this.optionApi
        }, this.selected);
    }
}"
    x-init="init()"
    class="relative">
    <select
        x-ref="{{ $xref ?? 'select' }}"
        x-model="selected"
        {{ $attributes->merge([
            'class' => 'invisible',
        ]) }}>
        <option value="" disabled>{{ $placeholder }}</option>
        <template x-for="option in options" :key="option.id">
            <option :value="option.id" x-text="option.name"></option>
        </template>
    </select>
</div>
