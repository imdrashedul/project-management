<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Enums\Priority;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Models\Task;
use App\Services\ProjectService;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskProvider)
    {
        // Empty Space Isn't Empty :)
    }

    /**
     * Handler for invoking the tasks route action implicitly.
     * @return mixed
     */
    public function __invoke(): mixed
    {
        return view('tasks.index', [
            "tasks" => $this->taskProvider->paginatedTasks()
        ]);
    }

    /**
     * @return mixed
     */
    public function create(ProjectService $projectService): mixed
    {
        return view('tasks.create', [
            "project" => !$projectService->isEmptyProject() ? $projectService->project() : null,
            "taskStatuses" => TaskStatus::valueCaseList(),
            "priorities" => Priority::valueCaseList()
        ]);
    }

    /**
     * @return mixed
     */
    public function createByTask(): mixed
    {
        // Redirects to previous or 404 If no task found
        if (!empty($resolve = $this->taskProvider->fallbackIfRequired())) {
            return $resolve;
        }

        $task = $this->taskProvider->task();

        return view('tasks.create', [
            "project" => $task->project,
            "task" => $task,
            "taskStatuses" => TaskStatus::valueCaseList(),
            "priorities" => Priority::valueCaseList()
        ]);
    }

    /**
     * @param \App\Http\Requests\TaskRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TaskRequest $request): RedirectResponse
    {
        $task = $this->taskProvider->create($request);

        $referrer = request()->get("referrer");

        $redirect = redirect(url()->previous() ?: route("tasks.index"))
            ->with("success", "Task created successfully!")
            ->with("task", $task->toArray());

        if (!empty($referrer)) {
            $redirect->with("xreferrer", $referrer);
        }

        return $redirect;
    }

    /**
     * @return mixed
     */
    public function show(): mixed
    {
        // Redirects to previous or 404 If no task found
        if (!empty($resolve = $this->taskProvider->fallbackIfRequired())) {
            return $resolve;
        }

        $task = $this->taskProvider->task();


        return view('tasks.show', [
            "task" => $task,
            "subtasks" => $task->subtask_count > 0 ? $task->paginatedSubtasks() : null
        ]);
    }

    /**
     * @return mixed
     */
    public function edit(): mixed
    {
        // Redirects to previous or 404 If no task found
        if (!empty($resolve = $this->taskProvider->fallbackIfRequired())) {
            return $resolve;
        }

        return view('tasks.edit', [
            "task" => $this->taskProvider->task(),
            "taskStatuses" => TaskStatus::valueCaseList(),
            "priorities" => Priority::valueCaseList()
        ]);
    }

    /**
     * @param \App\Http\Requests\TaskUpdateRequest $request
     * @return RedirectResponse
     */
    public function update(TaskUpdateRequest $request): RedirectResponse
    {
        // Redirects to previous or 404 If no task found
        if (!empty($resolve = $this->taskProvider->fallbackIfRequired())) {
            return $resolve;
        }

        $task = $this->taskProvider->update($request);

        $referrer = request()->get("referrer");

        $redirect = redirect(url()->previous() ?: route("tasks.index"))
            ->with("success", "Task updated successfully!")
            ->with("task", $task->toArray());

        if (!empty($referrer)) {
            $redirect->with("xreferrer", $referrer);
        }

        return $redirect;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(): RedirectResponse
    {
        // Redirects to previous or 404 If no task found
        if (!empty($resolve = $this->taskProvider->fallbackIfRequired())) {
            return $resolve;
        }

        $taskTitle = $this->taskProvider->task()->title;
        $taskUlid = $this->taskProvider->task()->ulid;

        $this->taskProvider->delete();

        return redirect(url()->previous() != route("tasks.show", ["task" => $taskUlid]) ? url()->previous() : route("tasks.index"))
            ->with("success", "Task {$taskTitle} deleted successfully!");
    }

    /**
     * Provides paginated list for jQuery Select2
     * @param \Illuminate\Http\Request $request
     * @param \App\Services\ProjectService $projectProvider
     * @return \Illuminate\Http\JsonResponse
     */
    public function select2(Request $request, ProjectService $projectProvider): JsonResponse
    {
        $keyword = $request->get("search");
        $projectUlid = $request->get("project");

        if (!empty($projectUlid) && !empty($project = $projectProvider->get_ulid($projectUlid))) {
            $task = $this->taskProvider->task()->where("project_id", $project->id);

            if (!empty($keyword)) {
                $task->where("title", "like", "%{$keyword}%");
            }

            $results = $task->paginate();

            return response()->json([
                "results" => $results->getCollection()->map(function (Task $task): array {
                    return [
                        "id" => $task->ulid,
                        "text" => $task->title
                    ];
                }),
                "pagination" => [
                    "more" => $results->hasMorePages()
                ]
            ]);
        }

        return response()->json([
            "results" => [],
            "pagination" => ["more" => false]
        ]);
    }

    /**
     * Provides option details based on id for jQuery Select2
     * Summary of select2single
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function select2single(Request $request): JsonResponse
    {
        if (!empty($id = $request->get("id")) && !empty($task = $this->taskProvider->get_ulid($id))) {
            if ($task->project->user_id == auth()->id()) {
                return response()->json([
                    "id" => $task->ulid,
                    "name" => $task->title
                ]);
            }
        }

        return response()->json([]);
    }

    public function uploadSubtask()
    {

    }

    public function importSubtask()
    {

    }
}
