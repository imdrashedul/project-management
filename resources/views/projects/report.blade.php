<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Report</title>
    @include('projects.partials.report-css')
</head>

<body>
    <div class="container">
        <div class="project-card">
            <div class="project-header">
                <div class="project-title">{{ $project->title }}</div>
                <div class="project-meta">
                    <p><strong>Deadline:</strong> {{ $project->deadline->format('j M Y') }}</p>
                    <p><strong>Status:</strong> {{ $project->status->case() }}</p>
                    <p><strong>Priority:</strong> {{ $project->priority->case() }}</p>
                </div>
            </div>

            <div class="project-details">
                <p class="label">Details:</p>
                <p>{!! $project->description !!}</p>
            </div>

            <table class="card-stats">
                <tr>
                    <td class="stat-box">
                        <div class="stat-title">Total Tasks</div>
                        <div class="stat-value">{{ $project->task_count ?: 'No task added' }}</div>
                    </td>
                    <td class="stat-box">
                        <div class="stat-title">Pending Tasks</div>
                        <div class="stat-value">{{ $project->pending_task_count ?: 'No Pending Task' }}</div>
                    </td>
                    <td class="stat-box">
                        <div class="stat-title">Last Updated</div>
                        <div class="stat-value">{{ $project->updated_at->format('j M Y g:i a') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <h3>Tasks</h3>
        <table class="task-table">
            <thead>
                <tr>
                    <th>Task Title</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Deadline</th>
                    <th>Total Subtasks</th>
                    <th>Pending Subtasks</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody>
                @if ($project->tasks->isNotEmpty())
                    @foreach ($project->tasks as $task)
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td>{{ $task->status->case() }}</td>
                            <td>{{ $task->priority->case() }}</td>
                            <td>{{ $task->deadline->format('j M Y') }}</td>
                            <td>{{ $task->subtasks_count ?? 0 }}</td>
                            <td>{{ $task->pending_subtasks_count ?? 0 }}</td>
                            <td>{{ $task->updated_at->format('j M Y g:i a') }}</td>
                        </tr>

                        @php $subtasks = $task->subtaskWithSubtasks() @endphp
                        @if ($subtasks->isNotEmpty())
                            <tr>
                                <td colspan="7">
                                    <h4 style="margin-bottom: 15px;">Subtasks of "{{ $task->title }}"</h4>
                                    <table class="subtask-table" style="margin-top: 15px;">
                                        <thead>
                                            <tr>
                                                <th>Subtask Title</th>
                                                <th>Status</th>
                                                <th>Priority</th>
                                                <th>Deadline</th>
                                                <th>Total Subtasks</th>
                                                <th>Pending Subtasks</th>
                                                <th>Last Updated</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($subtasks as $subtask)
                                                @include('projects.partials.report-subtasks', ['subtask' => $subtask, 'level' => 1])
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="text-center">
                            No Task Added
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</body>

</html>
