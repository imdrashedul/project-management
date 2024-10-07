@php $isUpdating = $updating ?? false; @endphp
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Project Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ $isUpdating ? __('Update project information.') : __('Fill up project information.') }}
        </p>
    </header>

    <form method="post" action="{{ $isUpdating ? route('projects.update', ['project' => $project->ulid]) : route('projects.store') }}" class="mt-6 space-y-6">
        @csrf
        @if ($isUpdating)
            @method('put')
        @endif
        <div>
            <x-input-label for="title" :value="__('Title')" required="true" />
            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $project->title ?? '')" placeholder="Enter project title" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('title')" />
        </div>
        <div>
            <x-input-label for="status" :value="__('Status')" required="true" />
            <x-select-input id="status" name="status" class="mt-1 block w-full" required>
                <x-slot name="options">
                    <option value="">{{ __('Select status') }}</option>
                    @foreach ($projectStatuses as $key => $value)
                        <option value="{{ $key }}" {{ old('status', $project->status->value ?? \App\Enums\ProjectStatus::Initiation->value) == $key ? 'selected' : '' }}>{{ __($value) }}</option>
                    @endforeach
                </x-slot>
            </x-select-input>
            <x-input-error class="mt-2" :messages="$errors->get('status')" />
        </div>
        <div>
            <x-input-label for="priority" :value="__('Priority')" required="true" />
            <x-select-input id="priority" name="priority" class="mt-1 block w-full" required>
                <x-slot name="options">
                    <option value="">{{ __('Select priority') }}</option>
                    @foreach ($priorities as $key => $value)
                        <option value="{{ $key }}" {{ old('priority', $project->priority->value ?? \App\Enums\Priority::Medium->value) == $key ? 'selected' : '' }}>{{ __($value) }}</option>
                    @endforeach
                </x-slot>
            </x-select-input>
            <x-input-error class="mt-2" :messages="$errors->get('priority')" />
        </div>
        <div>
            <x-input-label for="deadline" :value="__('Deadline')" required="true" />
            <x-datetime-input id="deadline" name="deadline" class="mt-1 block w-full" :value="old('deadline', $project->deadline_formatted ?? '')" required />
            <x-input-error class="mt-2" :messages="$errors->get('deadline')" />
        </div>
        <div>
            <x-input-label for="description" :value="__('Description')" required="true" />
            <x-summernote id="description" name="description" class="mt-1 block w-full" :value="old('description', $project->description ?? '')" required></x-summernote>
            <x-input-error class="mt-2" :messages="$errors->get('description')" />
        </div>
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __($isUpdating ? 'Save' : 'Create') }}</x-primary-button>
        </div>
    </form>
</section>
