<?php

namespace App\Services;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Http\Requests\ProjectRequest;
use App\Traits\Helpers\FallbackResolver;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Redirect;

class ProjectService
{
    use FallbackResolver;
    /**
     * @param \App\Models\Project $project
     */
    public function __construct(private Project $project)
    {
        // Empty Space Isn't Empty :)
    }

    /**
     * Used to create new project from ProjectRequest
     * @param \App\Http\Requests\ProjectRequest $request
     * @return Project
     */
    public function create(ProjectRequest $request): Project
    {
        $attributes = collect($request->validated())
            ->merge([
                "user_id" => Auth::id()
            ]);

        $this->project->fill($attributes->toArray())->save();

        return $this->project;
    }

    /**
     * Used to update an existing project
     * @param \App\Http\Requests\ProjectRequest $request
     * @return \App\Models\Project
     */
    public function update(ProjectRequest $request): Project
    {
        $attributes = Collection::make($request->validated());

        $this->project->update($attributes->toArray());

        return $this->project;
    }

    /**
     * Used to remove an existing project
     * @return bool|null
     */
    public function delete(): bool|null
    {
        return $this->project->delete();
    }

    /**
     * Used to fetch a single project by ulid
     * @param string $id
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Project|null
     */
    public function get_ulid(string $id): Collection|Project|null
    {
        return $this->project->where("ulid", $id)->first();
    }

    /**
     * Used to fetch a single project by int id
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Project|null
     */
    public function get(int $id): Collection|Project|null
    {
        return $this->project->find($id);
    }

    /**
     * @param mixed $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginatedProjects($perPage = null): LengthAwarePaginator
    {
        return $this->project->orderByRaw(
            sprintf("FIELD(`priority`, %s)", implode(",", [
                Priority::Critical->value,
                Priority::High->value,
                Priority::Medium->value,
                Priority::Low->value,
                Priority::Optional->value
            ]))
        )->orderBy("deadline")
            ->withCount([
                "tasks as task_count" => function ($query) {
                    $query->whereNull("parent_id");
                },
                "tasks as pending_task_count" => function ($query) {
                    $query->whereNull("parent_id")->whereIn("status", [
                        TaskStatus::InProgress,
                        TaskStatus::InReview,
                        TaskStatus::Assigned
                    ]);
                }
            ])
            ->where("user_id", auth()->id())
            ->paginate($perPage);
    }

    /**
     * @return \App\Models\Project
     */
    public function project(): Project
    {
        return $this->project;
    }

    /**
     * Used to redirect if null model for update, delete, show.
     * @return mixed
     */
    public function fallbackIfRequired(): mixed
    {
        return $this->resolveFallback($this->isEmptyProject());
    }

    /**
     * Determine if project instance is empty or not
     * @return bool
     */
    public function isEmptyProject(): bool
    {
        return !$this->project->exists && empty($this->project->getAttributes());
    }
}
