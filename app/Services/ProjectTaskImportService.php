<?php

namespace App\Services;
use App\Contracts\CsvImportableService;
use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\File\FileWrapper;
use App\Models\Project;
use App\Models\Task;
use App\Rules\WordCount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Validator;

class ProjectTaskImportService implements CsvImportableService
{
    private Collection $projects;
    private Collection $tasks;
    private Collection $subtasks;

    private Collection $projectCache;

    private Collection $taskCache;

    private $userId = null;

    public function __construct(protected CsvImportService $importer)
    {
        $this->projectCache = collect(); //
        $this->taskCache = collect(); //
        $this->userId = auth()->id(); //
    }

    protected function isProject(array $row): bool
    {
        return empty($row["project"]) && empty($row["parent"]);
    }

    protected function isTask(array $row): bool
    {
        return !empty($row["project"]) && empty($row["parent"]);
    }

    protected function isSubtask(array $row): bool
    {
        return !empty($row["parent"]);
    }

    protected function newUlid(): string
    {
        return strtolower((string) Str::ulid());
    }

    protected function getConstantValueByCase(string $constantClass, string $name, $default = 0)
    {
        try {
            return constant("$constantClass::$name")->value;
        } catch (\Exception $e) {
        }

        return $default;
    }

    protected function getConstantValue(string $enumClass, int $value, $default = 0)
    {
        try {
            $enumValue = $enumClass::from($value);
            return !empty($enumValue->name) ? $enumValue->value : $default;
        } catch (\Exception $e) {
        }

        return $default;
    }

    protected function getDeadline(string $deadline, Carbon $default = null)
    {
        $format = "Y-m-d H:i:s";
        $default = $default ?? now()->addDays(7);

        try {
            return Carbon::parse($deadline)->format($format);
        } catch (\Exception $e) {
        }

        return $default->format($format);
    }

    protected function getProjectIdByRow(int $row)
    {
        return --$row > 0 && !empty($project = $this->projects->get($row)) ? ($project["id"] ?? null) : null;
    }

    protected function getTaskByRow(int $row)
    {
        return --$row > 0 ? (
            !empty($task = $this->tasks->get($row)) ? $task : (
                !empty($subtask = $this->subtasks->get($row)) ? $subtask : null
            )
        ) : null;
    }

    protected function validateDetails(string $details): bool
    {
        $validator = Validator::make([
            "details" => $details
        ], [
            "details" => ["required", new WordCount(10)]
        ]);

        return !$validator->fails();
    }

    protected function getProjectByUlid(string $ulid)
    {
        if (!empty($project = $this->projectCache->get("ulid"))) {
            return $project;
        }

        if (!empty($project = Project::where(["ulid" => $ulid])->first())) {
            $this->projectCache->put($ulid, $project);
            return $project;
        }

        return null;
    }

    protected function getTaskByUlid(string $ulid)
    {
        if (!empty($task = $this->taskCache->get("ulid"))) {
            return $task;
        }

        if (!empty($task = Task::where(["ulid" => $ulid])->with(["project"])->first())) {
            $this->taskCache->put($ulid, $task);
            return $task;
        }

        return null;
    }

    protected function validated(array $row, string $statusClass, string $detailsColumn = "details"): array
    {
        $priorityClass = Priority::class;

        $status = filter_var($row["status"] ?? null, FILTER_VALIDATE_INT) !== false ?
            $this->getConstantValue($statusClass, $row["status"]) :
            $this->getConstantValueByCase($statusClass, $row["status"] ?? null);

        $priority = filter_var($row["priority"] ?? null, FILTER_VALIDATE_INT) !== false ?
            $this->getConstantValue($priorityClass, $row["priority"], 2) :
            $this->getConstantValueByCase($priorityClass, $row["priority"] ?? null, 2);

        $deadline = $this->getDeadline($row["deadline"] ?? null);

        $project_id = !empty($row["project"]) ? (
            Str::isUlid($row["project"]) ? (
                !empty($project = $this->getProjectByUlid($row["project"])) && $project->user_id == $this->userId ? $project->id : false
            ) : (
                filter_var($row["project"], FILTER_VALIDATE_INT) ?
                $this->getProjectIdByRow($row["project"]) :
                false
            )
        ) : null;

        if ($project_id === false)
            return [];

        $parent_id = !empty($row["parent"]) ? (
            Str::isUlid($row["parent"]) ? (
                !empty($task = $this->getTaskByUlid($row["parent"])) && $task->project->user_id == $this->userId && !empty($project_id = $task->project->id) ? $task->id : false
            ) : (
                filter_var($row["parent"], FILTER_VALIDATE_INT) ? (
                    !empty($task = $this->getTaskByRow($row["parent"])) && !empty($task["id"]) && !empty($project_id = $task["project_id"]) ? (
                        $task["id"]
                    ) : false
                ) : false
            )
        ) : null;

        if ($parent_id === false)
            return [];

        if (empty($row["title"]) || !(Str::length($row["title"]) <= 255))
            return [];

        if (!$this->validateDetails($row["details"] ?? null))
            return [];

        $validated = [
            "ulid" => $this->newUlid(),
            "title" => $row["title"],
            $detailsColumn => $row["details"],
            "status" => $status,
            "priority" => $priority,
            "deadline" => $deadline,
            "created_at" => now()->format("Y-m-d H:i:s"),
            "updated_at" => now()->format("Y-m-d H:i:s")
        ];

        if ($project_id !== false && !empty($project_id)) {
            $validated["project_id"] = $project_id;
        }

        if ($parent_id !== false && !empty($parent_id)) {
            $validated["parent_id"] = $parent_id;
        }

        return $validated;
    }

    protected function handleImported(Collection $imported, string $model, string $container)
    {
        $cases = collect(["CASE"]);
        $table = $model::table();
        $ulids = collect([]);

        $imported->each(function ($row, $rowId) use ($cases, $table, $ulids) {
            $rowId = (int) $rowId;
            $cases->push("WHEN `{$table}`.`ulid` = '{$row["ulid"]}' THEN {$rowId}");
            $ulids->push($row["ulid"]);
        });

        $cases->push("ELSE NULL END AS `row_id`");

        $collection = $model::select("{$table}.*", DB::raw($cases->join(" ")))->whereIn("ulid", $ulids->toArray())->get();
        if ($collection->isNotEmpty()) {
            $collection->each(function ($entry) use ($container) {
                $this->{$container}->put(
                    $entry->row_id,
                    array_merge($this->{$container}->get($entry->row_id, $entry->getRawOriginal()), ["id" => $entry->id])
                );
            });
        }
    }

    public function resolve(callable $import, Collection $records): mixed
    {
        $pendingTasksProcessing = collect();
        $pendingSubtasksProcessing = collect();
        $pendingOthertasks = collect();

        //Leveling and Import Primary Level Projects/Tasks/SubTasks
        $records->each(function ($row, $rowId) use ($pendingTasksProcessing, $pendingSubtasksProcessing) {
            if ($this->isProject($row)) {
                if (!empty($project = $this->validated($row, ProjectStatus::class, 'description'))) {
                    if (isset($project["parent_id"])) {
                        unset($project["parent_id"]);
                    }

                    if (isset($project["project_id"])) {
                        unset($project["project_id"]);
                    }

                    $project["user_id"] = $this->userId;

                    $this->projects->put($rowId, $project);
                }
            } else if ($this->isTask($row)) {
                if (
                    !empty($task = $this->validated($row, TaskStatus::class)) &&
                    !empty($task["project_id"])
                ) {
                    if (isset($task["parent_id"])) {
                        unset($task["parent_id"]);
                    }

                    $this->tasks->put($rowId, $task);
                } else {
                    $pendingTasksProcessing->put($rowId, $row);
                }
            } else if ($this->isSubtask($row)) {
                if (
                    !empty($task = $this->validated($row, TaskStatus::class)) &&
                    !empty($task["project_id"]) &&
                    !empty($task["parent_id"])
                ) {
                    $this->subtasks->put($rowId, $task);
                } else {
                    $pendingSubtasksProcessing->put($rowId, $row);
                }
            }
        });

        if ($this->projects->isNotEmpty()) {
            $import(Project::class, $this->projects, function ($imported) {
                $this->handleImported($imported, Project::class, "projects");
            });
        }

        if ($this->tasks->isNotEmpty()) {
            $import(Task::class, $this->tasks, function ($imported) {
                $this->handleImported($imported, Task::class, "tasks");
            });
        }

        if ($this->subtasks->isNotEmpty()) {
            $import(Task::class, $this->subtasks, function ($imported) {
                $this->handleImported($imported, Task::class, "subtasks");
            });
        }

        //Import Secondary Level Tasks
        if ($pendingTasksProcessing->isNotEmpty()) {
            $resolvedPendingTasks = collect();

            $pendingTasksProcessing->each(function ($row, $rowId) use ($resolvedPendingTasks) {
                if (
                    !empty($task = $this->validated($row, TaskStatus::class)) &&
                    !empty($task["project_id"])
                ) {
                    if (isset($task["parent_id"])) {
                        unset($task["parent_id"]);
                    }

                    $resolvedPendingTasks->put($rowId, $task);
                }
            });

            if ($resolvedPendingTasks->isNotEmpty()) {
                $import(Task::class, $resolvedPendingTasks, function ($imported) {
                    $this->handleImported($imported, Task::class, "tasks");
                });
            }
        }

        //Import Secondary Level Subtasks
        if ($pendingSubtasksProcessing->isNotEmpty()) {
            $resolvedPendingSubtasks = collect();

            $pendingSubtasksProcessing->each(function ($row, $rowId) use ($resolvedPendingSubtasks, $pendingOthertasks) {
                if (
                    !empty($task = $this->validated($row, TaskStatus::class)) &&
                    !empty($task["project_id"]) &&
                    !empty($task["parent_id"])
                ) {
                    $resolvedPendingSubtasks->put($rowId, $task);
                } else {
                    // Stored subtasks maybe subtask of another subtask to imeplement it in future
                    $pendingOthertasks->put($rowId, $row);
                }
            });

            if ($resolvedPendingSubtasks->isNotEmpty()) {
                $import(Task::class, $resolvedPendingSubtasks, function ($imported) {
                    $this->handleImported($imported, Task::class, "subtasks");
                });
            }
        }

        return "Projects/Tasks/Subtasks has been imported from given csv file successfully!";
    }

    public function import(FileWrapper $file)
    {
        $this->projects = collect();
        $this->tasks = collect();
        $this->subtasks = collect();
        $this->importer->queue($file, $this);
    }
}
