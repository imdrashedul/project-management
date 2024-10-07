<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Task Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Fill up task information.') }}
        </p>
    </header>

    <form method="post" action="{{ route('tasks.store') }}" class="mt-6 space-y-6">
        <input type="hidden" name="referrer" value="{{ $referrer }}" />
        @csrf
        <div>
            <x-input-label for="project" :value="__('Project')" required="true" />
            @if (!empty($project))
                <x-select-input x-ref="project" id="project" name="project" class="mt-1 block w-full" required>
                    <x-slot name="options">
                        <option value="{{ $project->ulid }}">{{ $project->title }}</option>
                    </x-slot>
                </x-select-input>
            @else
                <x-select2-input xref="project" id="project" name="project" class="mt-1 block w-full" required placeholder="Select a project" :value="old('project')" api="{{ route('projects.select2') }}" optionApi="{{ route('projects.select2.single') }}" />
            @endif
            <x-input-error class="mt-2" :messages="$errors->get('project')" />
        </div>
        <div>
            <x-input-label for="parent" :value="__('Parent Task')" />
            @if (!empty($task))
                <x-select-input id="parent" name="parent" class="mt-1 block w-full" required>
                    <x-slot name="options">
                        <option value="{{ $task->ulid }}">{{ $task->title }}</option>
                    </x-slot>
                </x-select-input>
            @else
                <x-select2-input dependency="project" id="parent" name="parent" class="mt-1 block w-full" placeholder="Select parent task" :value="old('parent')" api="{{ route('tasks.select2') }}" optionApi="{{ route('tasks.select2.single') }}" />
            @endif
            <x-input-error class="mt-2" :messages="$errors->get('parent')" />
        </div>
        <div>
            <x-input-label for="title" :value="__('Title')" required="true" />
            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" placeholder="Enter task title" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('title')" />
        </div>
        <div>
            <x-input-label for="status" :value="__('Status')" required="true" />
            <x-select-input id="status" name="status" class="mt-1 block w-full" required>
                <x-slot name="options">
                    <option value="">{{ __('Select status') }}</option>
                    @foreach ($taskStatuses as $key => $value)
                        <option value="{{ $key }}" {{ old('status', \App\Enums\TaskStatus::NotAssigned->value) == $key ? 'selected' : '' }}>{{ __($value) }}</option>
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
                        <option value="{{ $key }}" {{ old('priority', \App\Enums\Priority::Medium->value) == $key ? 'selected' : '' }}>{{ __($value) }}</option>
                    @endforeach
                </x-slot>
            </x-select-input>
            <x-input-error class="mt-2" :messages="$errors->get('priority')" />
        </div>
        <div>
            <x-input-label for="deadline" :value="__('Deadline')" required="true" />
            <x-datetime-input id="deadline" name="deadline" class="mt-1 block w-full" :value="old('deadline')" required />
            <x-input-error class="mt-2" :messages="$errors->get('deadline')" />
        </div>
        <div>
            <x-input-label for="details" :value="__('Details')" required="true" />
            <x-summernote id="details" name="details" class="mt-1 block w-full" :value="old('details')" required></x-summernote>
            <x-input-error class="mt-2" :messages="$errors->get('details')" />
        </div>
        <div class="flex items-center gap-4">
            <x-primary-button>Create</x-primary-button>
        </div>
    </form>
</section>
