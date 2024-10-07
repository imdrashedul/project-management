<x-app-layout>
    <x-slot name="header">
        <h2 class="text-x font-semibold text-purple-5 leading-tight">
            {{ __('Tasks') }}
        </h2>
    </x-slot>

    <x-slot name="headerButton">
        <x-link-button href="{{ route('tasks.create') }}" class="!py-0 !px-2 focus:ring-purple-600">
            {{ __('add') }}
            <i class="fa-solid fa-chevron-right ml-2"></i>
        </x-link-button>
    </x-slot>

    <div class="py-12">
        @if (session('success'))
            <x-success-alert>
                <strong>{{ __(session('success')) }}</strong>
            </x-success-alert>
        @endif

        @if (!$tasks->isEmpty())
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
                                            <span class="text-sm text-slate-500">Project</span>
                                            <span class="font-semibold">{{ $task->project->title }}</span>
                                        </div>
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
        @else
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col items-center justify-center">
                <div class="pl-6 text-purple-5 opacity-20 mt-10">
                    <svg width="300px" height="300px" viewBox="0 0 1024 1024" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg">
                        <path d="M660 103.2l-149.6 76 2.4 1.6-2.4-1.6-157.6-80.8L32 289.6l148.8 85.6v354.4l329.6-175.2 324.8 175.2V375.2L992 284.8z" fill="#FFFFFF" />
                        <path d="M180.8 737.6c-1.6 0-3.2 0-4-0.8-2.4-1.6-4-4-4-7.2V379.2L28 296c-2.4-0.8-4-4-4-6.4s1.6-5.6 4-7.2l320.8-191.2c2.4-1.6 5.6-1.6 8 0l154.4 79.2L656 96c2.4-1.6 4.8-0.8 7.2 0l332 181.6c2.4 1.6 4 4 4 7.2s-1.6 5.6-4 7.2l-152.8 88v350.4c0 3.2-1.6 5.6-4 7.2-2.4 1.6-5.6 1.6-8 0l-320-174.4-325.6 173.6c-1.6 0.8-2.4 0.8-4 0.8zM48 289.6L184.8 368c2.4 1.6 4 4 4 7.2v341.6l317.6-169.6c2.4-1.6 5.6-1.6 7.2 0l312.8 169.6V375.2c0-3.2 1.6-5.6 4-7.2L976 284.8 659.2 112.8 520 183.2c0 0.8-0.8 0.8-0.8 1.6-2.4 4-7.2 4.8-11.2 2.4l-1.6-1.6h-0.8l-152.8-78.4L48 289.6z" fill="#6A576D" />
                        <path d="M510.4 179.2l324.8 196v354.4L510.4 554.4z" fill="#121519" />
                        <path d="M510.4 179.2L180.8 375.2v354.4l329.6-175.2z" fill="#121519" />
                        <path d="M835.2 737.6c-1.6 0-2.4 0-4-0.8l-324.8-176c-2.4-1.6-4-4-4-7.2V179.2c0-3.2 1.6-5.6 4-7.2 2.4-1.6 5.6-1.6 8 0L839.2 368c2.4 1.6 4 4 4 7.2v355.2c0 3.2-1.6 5.6-4 7.2h-4zM518.4 549.6l308.8 167.2V379.2L518.4 193.6v356z" fill="#6A576D" />
                        <path d="M180.8 737.6c-1.6 0-3.2 0-4-0.8-2.4-1.6-4-4-4-7.2V375.2c0-3.2 1.6-5.6 4-7.2l329.6-196c2.4-1.6 5.6-1.6 8 0 2.4 1.6 4 4 4 7.2v375.2c0 3.2-1.6 5.6-4 7.2l-329.6 176h-4z m8-358.4v337.6l313.6-167.2V193.6L188.8 379.2z" fill="#6A576D" />
                        <path d="M510.4 550.4L372 496 180.8 374.4v355.2l329.6 196 324.8-196V374.4L688.8 483.2z" fill="#D6AB7F" />
                        <path d="M510.4 933.6c-1.6 0-3.2 0-4-0.8L176.8 736.8c-2.4-1.6-4-4-4-7.2V374.4c0-3.2 1.6-5.6 4-7.2 2.4-1.6 5.6-1.6 8 0L376 488.8l135.2 53.6 174.4-66.4L830.4 368c2.4-1.6 5.6-2.4 8-0.8 2.4 1.6 4 4 4 7.2v355.2c0 3.2-1.6 5.6-4 7.2l-324.8 196s-1.6 0.8-3.2 0.8z m-321.6-208l321.6 191.2 316.8-191.2V390.4L693.6 489.6c-0.8 0.8-1.6 0.8-1.6 0.8l-178.4 68c-1.6 0.8-4 0.8-5.6 0L369.6 504c-0.8 0-0.8-0.8-1.6-0.8L188.8 389.6v336z" fill="#6A576D" />
                        <path d="M510.4 925.6l324.8-196V374.4L665.6 495.2l-155.2 55.2z" fill="#121519" />
                        <path d="M510.4 933.6c-1.6 0-2.4 0-4-0.8-2.4-1.6-4-4-4-7.2V550.4c0-3.2 2.4-6.4 5.6-7.2L662.4 488l168-120c2.4-1.6 5.6-1.6 8-0.8 2.4 1.6 4 4 4 7.2v355.2c0 3.2-1.6 5.6-4 7.2l-324.8 196s-1.6 0.8-3.2 0.8z m8-377.6v355.2l308.8-185.6V390.4L670.4 501.6c-0.8 0.8-1.6 0.8-1.6 0.8l-150.4 53.6z" fill="#6A576D" />
                        <path d="M252.8 604l257.6 145.6V550.4l-147.2-49.6-182.4-126.4z" fill="#121519" />
                        <path d="M32 460l148.8-85.6 329.6 176L352 640.8z" fill="#FFFFFF" />
                        <path d="M659.2 693.6l176-90.4V375.2L692 480.8l-179.2 68-2.4 1.6z" fill="#121519" />
                        <path d="M510.4 550.4l148.8 85.6L992 464.8l-156.8-89.6z" fill="#FFFFFF" />
                        <path d="M352 648.8c-1.6 0-2.4 0-4-0.8l-320-180.8c-2.4-1.6-4-4-4-7.2s1.6-5.6 4-7.2L176.8 368c2.4-1.6 5.6-1.6 8 0l329.6 176c2.4 1.6 4 4 4 7.2s-1.6 5.6-4 7.2L356 648c-0.8 0.8-2.4 0.8-4 0.8zM48 460L352 632l141.6-80.8L180.8 384 48 460z" fill="#6A576D" />
                        <path d="M659.2 644c-1.6 0-2.4 0-4-0.8L506.4 557.6c-2.4-1.6-4-4-4-7.2s1.6-5.6 4-7.2l324.8-176c2.4-1.6 5.6-1.6 8 0l156.8 90.4c2.4 1.6 4 4 4 7.2s-1.6 5.6-4 7.2L663.2 643.2c-1.6 0.8-2.4 0.8-4 0.8zM527.2 550.4l132.8 76L976 464l-141.6-80-307.2 166.4z" fill="#6A576D" />
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-semibold text-slate-400 select-none">No Task Found</span>
                </div>
            </div>
        @endif
    </div>

</x-app-layout>
