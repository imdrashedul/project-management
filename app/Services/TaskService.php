<?php

namespace App\Services;

use App\Http\Requests\TaskRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Models\Task;
use App\Traits\Helpers\FallbackResolver;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Enums\Priority;
use App\Enums\TaskStatus;

class TaskService
{
    use FallbackResolver;

    public function __construct(private ProjectService $projectProvider, private Task $task)
    {
        // Empty Space Isn't Empty :)
    }

    /**
     * Used to create new task or subtask from TaskRequest
     * @param \App\Http\Requests\TaskRequest $request
     * @return Task
     */
    public function create(TaskRequest $request): Task
    {
        $attributes = collect($request->validated());
        $attributes = $attributes->merge([
            "project_id" => $this->projectProvider->get_ulid($attributes->get("project"))->id
        ]);

        if (!empty($parent_id = $attributes->get("parent"))) {
            $attributes = $attributes->merge([
                "parent_id" => $this->get_ulid($parent_id)->id
            ]);
        }

        $this->task->fill($attributes->toArray())->save();

        return $this->task;
    }

    /**
     * Used to update an existing task or subtask
     * @param \App\Http\Requests\TaskUpdateRequest $request
     * @return \App\Models\Task
     */
    public function update(TaskUpdateRequest $request): Task
    {
        $attributes = collect($request->validated());

        $requestedParent = $attributes->get("parent");

        if (!(empty($requestedParent) and empty($task->parent_id))) {
            if (!empty($requestedParent)) {
                $requestedParent = $this->get_ulid($requestedParent);
                if ($requestedParent->id != $this->task->parent_id) {
                    $attributes = $attributes->merge([
                        "parent_id" => $requestedParent->id
                    ]);
                }
            } else {
                $attributes = $attributes->merge([
                    "parent_id" => null
                ]);
            }
        }

        $this->task->update($attributes->toArray());

        return $this->task;
    }

    /**
     * Used to remove an existing task or subtask
     * @return bool|null
     */
    public function delete(): bool|null
    {
        return $this->task->delete();
    }

    /**
     * Used to fetch a single task by ulid
     * @param string $id
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Task|null
     */
    public function get_ulid(string $id): Collection|Task|null
    {
        return $this->task->where("ulid", $id)->first();
    }

    /**
     * Used to fetch a single task by int id
     * @param string $id
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Task|null
     */
    public function get(int $id): Collection|Task|null
    {
        return $this->task->find($id);
    }

    public function paginatedTasks($perPage = null): LengthAwarePaginator
    {
        return $this->task->orderByRaw(
            sprintf("FIELD(`priority`, %s)", implode(",", [
                Priority::Critical->value,
                Priority::High->value,
                Priority::Medium->value,
                Priority::Low->value,
                Priority::Optional->value
            ]))
        )->whereHas("project", function ($query) {
            $query->where("user_id", auth()->id());
        })->whereNull("parent_id")->orderBy("deadline")
            ->withCount("subtasks as subtask_count")
            ->withCount([
                "subtasks as pending_subtask_count" => function ($query) {
                    $query->whereIn("status", [
                        TaskStatus::InProgress,
                        TaskStatus::InReview,
                        TaskStatus::Assigned,
                        TaskStatus::NotStarted
                    ]);
                }
            ])->with(["parent", "project"])->paginate($perPage);
    }

    /**
     * @return \App\Models\Task
     */
    public function task(): Task
    {
        return $this->task;
    }


    /**
     * Used to redirect if null model for update, delete, show.
     * @return mixed
     */
    public function fallbackIfRequired(): mixed
    {
        return $this->resolveFallback($this->isEmptyTask());
    }

    /**
     * Determine if project instance is empty or not
     * @return bool
     */
    public function isEmptyTask(): bool
    {
        return !$this->task->exists && empty($this->task->getAttributes());
    }
}
