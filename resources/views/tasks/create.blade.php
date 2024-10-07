@php
    $referrer = request()->get('referrer', session('xreferrer'));
    $referrer = empty($referrer) ? (url()->previous() != url()->current() ? url()->previous() : route('projects.index')) : $referrer;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-x font-semibold text-purple-5 leading-tight">
            {{ __('New Task') }}
        </h2>
    </x-slot>

    <x-slot name="headerButton">
        <x-link-button href="{{ $referrer }}" class="!py-0 !px-2 focus:ring-purple-600 bg-red-800">
            <i class="fa-solid fa-chevron-left mr-2"></i>
            {{ __('Back') }}
        </x-link-button>
    </x-slot>

    <div class="py-12">

        @if (session('success'))
            <x-success-alert>
                <strong>{{ __(session('success')) }}</strong>
            </x-success-alert>
        @endif

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-purple-015 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('tasks.partials.form-create')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
