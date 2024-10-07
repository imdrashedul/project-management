<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ApiCollection;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TaskCollection;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProjectController extends Controller
{
    private $apiCollection;
    private $projectResource;

    public function __construct(private ProjectService $projectProvider)
    {
        $this->apiCollection = new ApiCollection();
        $this->projectResource = fn($resource) => new ProjectResource($resource);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/projects",
     *     summary="Get a list of paginated projects",
     *     description="Returns a list of projects with pagination",
     *     tags={"Projects"},
     *     security={{"JwtToken": {}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         description="Page number for pagination"
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", default=15),
     *         description="Number of projects per page"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of paginated projects",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessProjectPaginated")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorUnauthenticated")
     *     )
     * )
     */
    public function index(Request $request): ProjectCollection
    {
        return new ProjectCollection(
            $this->projectProvider->paginatedProjects($request->get("per_page"))
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/projects/{id}",
     *     summary="Get a single project by its ID",
     *     tags={"Projects"},
     *     security={{"JwtToken": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="The ID of the project"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Details of a single project",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessProjectFetch")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorProjectNotFound")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorUnauthenticated")
     *     )
     * )
     */
    public function show(): ApiCollection
    {
        if ($this->projectProvider->isEmptyProject()) {
            return $this->apiCollection->message("Project not found")->errors([
                "project" => "Bad request or project not exists"
            ])->statusCode(404);
        }

        return $this->apiCollection->message("Project fetched")->success([
            "project" => ($this->projectResource)($this->projectProvider->project())
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/projects",
     *     summary="Create a new project",
     *     tags={"Projects"},
     *     security={{"JwtToken": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProjectRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Project created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessProjectCreated")
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorProjectValidation")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorUnauthenticated")
     *     )
     * )
     */
    public function store(ProjectRequest $request): ApiCollection
    {
        $project = $this->projectProvider->create($request);

        $project->task_count = 0;
        $project->pending_task_count = 0;

        return $this->apiCollection->message("Project created")->success([
            "project" => ($this->projectResource)($project)
        ])->statusCode(201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/projects/{id}",
     *     summary="Update a project",
     *     tags={"Projects"},
     *     security={{"JwtToken": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="The ID of the project to update"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProjectRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessProjectUpdated")
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorProjectValidation")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorProjectNotFound")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorUnauthenticated")
     *     )
     * )
     */
    public function update(ProjectRequest $request): ApiCollection
    {
        if ($this->projectProvider->isEmptyProject()) {
            return $this->apiCollection->message("Project not found")->errors([
                "project" => "Bad request or project not exists"
            ])->statusCode(404);
        }

        $oldProject = clone $this->projectProvider->project();
        $project = $this->projectProvider->update($request);

        return $this->apiCollection->message("Project updated")->success([
            "project" => [
                "old" => ($this->projectResource)($oldProject),
                "new" => ($this->projectResource)($project)
            ]
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/projects/{id}",
     *     summary="Delete a project",
     *     tags={"Projects"},
     *     security={{"JwtToken": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="The ID of the project to delete"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Project deleted successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessProjectDeleted")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorProjectNotFound")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorUnauthenticated")
     *     )
     * )
     */
    public function destroy(): ApiCollection
    {
        if ($this->projectProvider->isEmptyProject()) {
            return $this->apiCollection->message("Project not found")->errors([
                "project" => "Bad request or project not exists"
            ])->statusCode(404);
        }

        $project = clone $this->projectProvider->project();
        $this->projectProvider->delete();

        return $this->apiCollection->message("Project deleted")->success([
            "project" => [
                "deleted" => ($this->projectResource)($project)
            ]
        ])->statusCode(204);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/projects/{id}/tasks",
     *     summary="Get tasks for a project",
     *     tags={"Projects", "Tasks"},
     *     security={{"JwtToken": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="The ID of the project"
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         description="Page number for pagination"
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", default=15),
     *         description="Number of projects per page"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of tasks for the project",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessTasksPaginated")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Project not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorProjectNotFound")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorUnauthenticated")
     *     )
     * )
     */
    public function tasks(Request $request): TaskCollection|ApiCollection
    {
        if ($this->projectProvider->isEmptyProject()) {
            return $this->apiCollection->message("Project not found")->errors([
                "project" => "Bad request or project not exists"
            ])->statusCode(404);
        }

        return new TaskCollection(
            $this->projectProvider->project()->paginatedTasks(
                $request->get("per_page")
            )
        );
    }
}
