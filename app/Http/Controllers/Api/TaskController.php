<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TaskRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\ApiCollection;
use \Illuminate\Http\Request;

class TaskController extends Controller
{
    private $apiCollection;
    private $taskResource;

    public function __construct(private TaskService $taskProvider)
    {
        $this->apiCollection = new ApiCollection();
        $this->taskResource = fn($resource) => new TaskResource($resource);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tasks",
     *     summary="Get a list of paginated tasks",
     *     description="Returns a list of tasks with pagination",
     *     tags={"Tasks"},
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
     *         description="Number of tasks per page"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of paginated tasks",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessTasksPaginated")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorUnauthenticated")
     *     )
     * )
     */
    public function index(Request $request): TaskCollection
    {
        return new TaskCollection(
            $this->taskProvider->paginatedTasks($request->get("per_page"))
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tasks/{id}",
     *     summary="Get a single task by its ID",
     *     tags={"Tasks"},
     *     security={{"JwtToken": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="The ID of the task"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Details of a single task",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessTaskFetch")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorTaskNotFound")
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
        if ($this->taskProvider->isEmptyTask()) {
            return $this->apiCollection->message("Task not found")->errors([
                "task" => "Bad request or task not exists"
            ])->statusCode(404);
        }

        return $this->apiCollection->message("Task fetched")->success([
            "task" => ($this->taskResource)($this->taskProvider->task())
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/tasks",
     *     summary="Create a new task",
     *     tags={"Tasks"},
     *     security={{"JwtToken": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TaskCreateRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Task created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessTaskCreated")
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorTaskCreateValidation")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorUnauthenticated")
     *     )
     * )
     */
    public function store(TaskRequest $request)
    {
        $task = $this->taskProvider->create($request);

        return $this->apiCollection->message("Task created")->success([
            "task" => ($this->taskResource)($task)
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/tasks/{id}",
     *     summary="Update a task",
     *     tags={"Tasks"},
     *     security={{"JwtToken": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="The ID of the task to update"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TaskUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessTaskUpdated")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorTaskNotFound")
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(ref="#/components/schemas/ErrorTaskUpdateValidation")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorUnauthenticated")
     *     )
     * )
     */
    public function update(TaskUpdateRequest $request): ApiCollection
    {
        if ($this->taskProvider->isEmptyTask()) {
            return $this->apiCollection->message("Task not found")->errors([
                "task" => "Bad request or task not exists"
            ])->statusCode(404);
        }

        $oldTask = clone $this->taskProvider->task();
        $task = $this->taskProvider->update($request);

        return $this->apiCollection->message("Task updated")->success([
            "task" => [
                "old" => ($this->taskResource)($oldTask),
                "new" => ($this->taskResource)($task)
            ]
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/task/{id}",
     *     summary="Delete a task",
     *     tags={"Tasks"},
     *     security={{"JwtToken": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="The ID of the task to delete"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Task deleted successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessTaskDeleted")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorTaskNotFound")
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
        if ($this->taskProvider->isEmptyTask()) {
            return $this->apiCollection->message("Task not found")->errors([
                "task" => "Bad request or task not exists"
            ])->statusCode(404);
        }

        $task = clone $this->taskProvider->task();
        $this->taskProvider->delete();

        return $this->apiCollection->message("Task deleted")->success([
            "task" => [
                "deleted" => ($this->taskResource)($task)
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tasks/{id}/subtasks",
     *     summary="Get subtasks for a task",
     *     tags={"Tasks"},
     *     security={{"JwtToken": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="The ID of the task"
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
     *         description="Number of subtasks per page"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of subtasks for the task",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessTasksPaginated")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorTaskNotFound")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorUnauthenticated")
     *     )
     * )
     */
    public function subtasks(Request $request): TaskCollection|ApiCollection
    {
        if ($this->taskProvider->isEmptyTask()) {
            return $this->apiCollection->message("Parent task not found")->errors([
                "task" => "Bad request or parent task not exists"
            ])->statusCode(404);
        }

        return new TaskCollection(
            $this->taskProvider->task()->paginatedSubtasks(
                $request->get("per_page")
            )
        );
    }
}
