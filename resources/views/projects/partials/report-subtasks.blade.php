<tr>
    <td>{{ $subtask->title }}</td>
    <td>{{ $subtask->status->case() }}</td>
    <td>{{ $subtask->priority->case() }}</td>
    <td>{{ $subtask->deadline->format('j M Y') }}</td>
    <td>{{ $subtask->subtasks_count ?? 0 }}</td>
    <td>{{ $subtask->pendinng_subtasks_count ?? 0 }}</td>
    <td>{{ $subtask->updated_at->format('j M Y g:i a') }}</td>
</tr>
@php $subtaskSubtasks = $subtask->subtaskWithSubtasks() @endphp
@if ($subtaskSubtasks->isNotEmpty())
    <tr>
        <td colspan="7">
            <table class="subtask-table">
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
                    @foreach ($subtaskSubtasks as $nestedSubtask)
                        @include('projects.partials.report-subtasks', ['subtask' => $nestedSubtask, 'level' => $level + 1])
                    @endforeach
                </tbody>
            </table>
        </td>
    </tr>
@endif
