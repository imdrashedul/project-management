<x-app-layout>
    <x-slot name="header">
        <h2 class="text-x font-semibold text-purple-5 leading-tight">
            Project: {{ $project->title }}
        </h2>
    </x-slot>

    <x-slot name="headerButton">
        <x-link-button href="{{ route('projects.index') }}" class="!py-0 !px-2 focus:ring-purple-600 bg-red-800">
            <i class="fa-solid fa-chevron-left mr-2"></i>
            {{ __('Projects') }}
        </x-link-button>

        <x-link-button href="{{ route('tasks.create', ['project' => $project->ulid]) }}" class="!py-0 !px-2">
            {{ __('Add Task') }}
            <i class="fa-solid fa-chevron-right ml-2"></i>
        </x-link-button>
    </x-slot>

    <div class="py-12">

        @if (session('success'))
            <x-success-alert>
                <strong>{{ __(session('success')) }}</strong>
            </x-success-alert>
        @endif

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-purple-015 shadow sm:rounded-lg">
                <div>
                    <div class="flex-1 flex flex-col justify-between gap-6">
                        <div class="flex">
                            <div class="flex-1">
                                <span class="font-semibold text-lg">{{ $project->title }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-lg"><i class="fa-solid fa-bomb text-red-400"></i> {{ $project->deadline->format('j M Y') }}</span>
                            </div>
                        </div>
                        <div class="flex justify-between gap-4">
                            <div class="flex flex-col">
                                <span class="text-sm text-slate-500">Status</span>
                                <span class="font-semibold">{{ $project->status->case() }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-slate-500">Priority</span>
                                <span class="font-semibold">{{ $project->priority->case() }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-slate-500">Total Tasks</span>
                                <span class="font-semibold">{{ $project->task_count ?: 'No task added' }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-slate-500">Pending Tasks</span>
                                <span class="font-semibold">{{ $project->pending_task_count ?: 'No Pending Task' }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm text-slate-500">Description</span>
                            <div class="flex-1">
                                <p>
                                    {!! $project->description !!}
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-1 flex gap-4">
                                <span class="text-sm text-slate-500 uppercase"><i class="fa-regular fa-clock mr-2"></i> {{ $project->created_at->format('j M Y g:i a') }}</span>
                                @if ($project->updated_at->ne($project->created_at))
                                    <span class="text-sm text-slate-500 uppercase"><i class="fa-solid fa-clock-rotate-left mr-2"></i> {{ $project->updated_at->format('j M Y g:i a') }}</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-center gap-4">
                                <div>
                                    <x-link-button href="{{ route('projects.report', ['project' => $project->ulid]) }}" class="!p-1 !px-2 bg-blue-700"><i class="fa-solid fa-file-arrow-down"></i></x-link-button>
                                </div>
                                <div>
                                    <x-link-button href="{{ route('projects.edit', ['project' => $project->ulid]) }}" class="!p-1 bg-slate-500"><i class="fa-solid fa-pen-clip"></i></x-link-button>
                                </div>
                                <div>
                                    <x-delete-confirm-button
                                        class="!p-1 bg-pink-700 focus:bg-red-900 hover:bg-red-900"
                                        itemName="the project {{ $project->title }}"
                                        actionUrl="{{ route('projects.destroy', ['project' => $project->ulid]) }}">
                                        <i class="fa-solid fa-trash"></i>
                                    </x-delete-confirm-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        @if (!empty($tasks) && !$tasks->isEmpty())
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 my-6">
                <div class="flex gap-4">
                    <div class="flex-1"><span class="text-slate-400">{{ __('Tasks') }}</span></div>
                </div>
            </div>
            @php $offset = ($tasks->currentPage() - 1) * $tasks->perPage(); @endphp
            @foreach ($tasks as $task)
                @php $serial =  $offset + $loop->iteration; @endphp
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-6">
                    <div class="bg-purple-015 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-purple-5">
                            <div class="flex gap-4">
                                <div class="w-20 h-[9rem] bg-slate-200 rounded flex items-center justify-center text-slate-500 select-none">
                                    # {{ $serial }}
                                </div>
                                <div class="flex-1 flex flex-col justify-between gap-2">
                                    <div class="flex">
                                        <div class="flex-1">
                                            <span class="font-semibold text-lg">{{ $task->title }}</span>
                                        </div>
                                        <div>
                                            <span class="font-semibold text-lg"><i class="fa-solid fa-bomb text-red-400"></i> {{ $task->deadline->format('j M Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="flex justify-between gap-4">
                                        <div class="flex flex-col">
                                            <span class="text-sm text-slate-500">Status</span>
                                            <span class="font-semibold">{{ $task->status->case() }}</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm text-slate-500">Priority</span>
                                            <span class="font-semibold">{{ $task->priority->case() }}</span>
                                        </div>
                                        @if (!($task->pending_subtask_count > 0))
                                            <div class="flex flex-col">
                                                <span class="text-sm text-slate-500">Sub-Tasks</span>
                                                <span class="font-semibold">{{ 0 ?: 'No Subtask Added' }}</span>
                                            </div>
                                        @endif
                                        @if ($task->subtask_count > 0)
                                            <div class="flex flex-col">
                                                <span class="text-sm text-slate-500">Pending Sub-Tasks</span>
                                                <span class="font-semibold">{{ $task->pending_subtask_count ?: 'No Pending Subtask' }}</span>
                                            </div>
                                        @endif
                                        <div class="flex items-center justify-center gap-4">
                                            <div>
                                                <x-link-button href="{{ route('tasks.edit', ['task' => $task->ulid]) }}" class="!p-1 bg-slate-500"><i class="fa-solid fa-pen-clip"></i></x-link-button>
                                            </div>
                                            <div>
                                                <x-delete-confirm-button
                                                    class="!p-1 bg-pink-700 focus:bg-red-900 hover:bg-red-900"
                                                    itemName="the task {{ $task->title }}"
                                                    actionUrl="{{ route('tasks.destroy', ['task' => $task->ulid]) }}">
                                                    <i class="fa-solid fa-trash"></i>
                                                </x-delete-confirm-button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-end">
                                        <div class="flex-1">
                                            <p>
                                                {{ Str::limit(html_entity_decode(strip_tags($task->details)), 250, '...') }}
                                            </p>
                                        </div>
                                        <div>
                                            <x-link-button href="{{ route('tasks.show', ['task' => $task->ulid]) }}" class="!py-1">View More <i class="fa-solid fa-chevron-right ml-2"></i></x-link-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="pl-6 text-purple-5">
                    {{ $tasks->links() }}
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
