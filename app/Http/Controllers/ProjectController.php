<?php

namespace App\Http\Controllers;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * @param \App\Services\ProjectService $projectProvider
     */
    public function __construct(private ProjectService $projectProvider)
    {
        // Empty Space Isn't Empty :)
    }

    /**
     * Handler for invoking the projects route action implicitly.
     * @return mixed
     */
    public function __invoke(): mixed
    {
        return view('projects.index', [
            "projects" => $this->projectProvider->paginatedProjects()
        ]);
    }

    /**
     * @return mixed
     */
    public function create(): mixed
    {
        return view('projects.create', [
            "projectStatuses" => ProjectStatus::valueCaseList(),
            "priorities" => Priority::valueCaseList()
        ]);
    }

    /**
     * @param \App\Http\Requests\ProjectRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProjectRequest $request): RedirectResponse
    {
        $project = $this->projectProvider->create($request);

        return redirect(url()->previous() ?: route("projects.index"))
            ->with("success", "Project created successfully!")
            ->with("project", $project->toArray());
    }

    /**
     * @return mixed
     */
    public function show(): mixed
    {
        // Redirects to previous or 404 If no project found
        if (!empty($resolve = $this->projectProvider->fallbackIfRequired())) {
            return $resolve;
        }

        $project = $this->projectProvider->project();

        return view('projects.show', [
            "project" => $project,
            "tasks" => $project->task_count > 0 ? $project->paginatedTasks() : null
        ]);
    }

    /**
     * @return mixed
     */
    public function edit(): mixed
    {
        // Redirects to previous or 404 If no project found
        if (!empty($resolve = $this->projectProvider->fallbackIfRequired())) {
            return $resolve;
        }

        return view('projects.edit', [
            "project" => $this->projectProvider->project(),
            "projectStatuses" => ProjectStatus::valueCaseList(),
            "priorities" => Priority::valueCaseList()
        ]);
    }

    /**
     * @param \App\Models\Project $project
     * @param \App\Http\Requests\ProjectRequest $request
     * @return RedirectResponse
     */
    public function update(ProjectRequest $request): RedirectResponse
    {
        // Redirects to previous or 404 If no project found
        if (!empty($resolve = $this->projectProvider->fallbackIfRequired())) {
            return $resolve;
        }

        $project = $this->projectProvider->update($request);

        return redirect(url()->previous() ?: route("projects.index"))
            ->with("success", "Project updated successfully!")
            ->with("project", $project->toArray());
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(): RedirectResponse
    {
        // Redirects to previous or 404 If no project found
        if (!empty($resolve = $this->projectProvider->fallbackIfRequired())) {
            return $resolve;
        }

        //
        $projectTitle = $this->projectProvider->project()->title;
        $projectUlid = $this->projectProvider->project()->ulid;

        $this->projectProvider->delete();

        return redirect(url()->previous() != route("projects.show", ["project" => $projectUlid]) ? url()->previous() : route("projects.index"))
            ->with("success", "Project {$projectTitle} deleted successfully!");
    }

    /**
     * Provides paginated list for jQuery Select2
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function select2(Request $request): JsonResponse
    {
        $keyword = $request->get("search");

        $project = $this->projectProvider->project()->where("user_id", auth()->id());

        if (!empty($keyword)) {
            $project->where("title", "like", "%{$keyword}%");
        }

        $results = $project->paginate();

        return response()->json([
            "results" => $results->getCollection()->map(function (Project $project): array {
                return [
                    "id" => $project->ulid,
                    "text" => $project->title
                ];
            }),
            "pagination" => [
                "more" => $results->hasMorePages()
            ]
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
        if (!empty($id = $request->get("id")) && !empty($project = $this->projectProvider->get_ulid($id))) {
            if ($project->user_id == auth()->id()) {
                return response()->json([
                    "id" => $project->ulid,
                    "name" => $project->title
                ]);
            }
        }

        return response()->json([]);
    }
}
