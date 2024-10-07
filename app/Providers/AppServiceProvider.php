<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Rules\WordCount;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * @OA\Info(
     *     title="Project Task Management",
     *     version="1.0.0",
     *     description="Sample Project to Demonstrate Laravel API Implementation with Passport.",
     *     @OA\Contact(
     *         email="route.imdrashedul@gmail.com"
     *     ),
     *     @OA\License(
     *         name="Apache 2.0",
     *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *     )
     * )
     *
     * @OA\Schema(
     *     schema="Project",
     *     type="object",
     *     required={"id", "title", "status", "priority", "description"},
     *     @OA\Property(property="id", type="string", example="01j99vx286z90vhmebjnj7rycw", description="Project ID"),
     *     @OA\Property(property="title", type="string", example="Sample Project", description="Project title"),
     *     @OA\Property(property="status", type="string", example="Development", description="Name of Status. Enums\ProjectStatus->name"),
     *     @OA\Property(property="status_code", type="integer", example=4, description="Status Code. Enums\ProjectStatus->value"),
     *     @OA\Property(property="priority", type="string", example="High", description="Project priority. Enums\Priority->name"),
     *     @OA\Property(property="priority_code", type="integer", description="Priority Code. Enums\Priority->value"),
     *     @OA\Property(property="description", type="string", description="Project description. Rich Text"),
     *     @OA\Property(property="deadline", type="string", format="date-time", description="Project deadline."),
     *     @OA\Property(property="created_at", type="string", format="date-time", description="Project creation time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", description="Project update time"),
     *     @OA\Property(property="task_count", type="integer", description="Number of tasks"),
     *     @OA\Property(property="pending_task_count", type="integer", description="Number of pending tasks")
     * )
     *
     * @OA\Schema(
     *     schema="Task",
     *     type="object",
     *     @OA\Property(property="id", type="string", example="01j99vx286z90vhmebjnj7rycw", description="Task ID"),
     *     @OA\Property(property="title", type="string", example="Sample Task", description="Task title"),
     *     @OA\Property(property="status", type="string", example="In Progress", description="Name of Status. Enums\TaskStatus->name"),
     *     @OA\Property(property="status_code", type="integer", example=1, description="Status Code. Enums\TaskStatus->value"),
     *     @OA\Property(property="priority", type="string", example="High", description="Task priority. Enums\Priority->name"),
     *     @OA\Property(property="priority_code", type="integer", description="Priority Code. Enums\Priority->value"),
     *     @OA\Property(property="details", type="string", description="Task details. Rich Text"),
     *     @OA\Property(property="deadline", type="string", format="date-time", description="Task deadline."),
     *     @OA\Property(property="created_at", type="string", format="date-time", description="Task creation time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", description="Task update time"),
     *     @OA\Property(property="subtask_count", type="integer", description="Number of Subtasks"),
     *     @OA\Property(property="pending_subtask_count", type="integer", description="Number of pending Subtasks"),
     *     @OA\Property(property="project", type="object", ref="#/components/schemas/Project"),
     *     @OA\Property(property="parent", type="object", ref="#/components/schemas/Task", nullable=true)
     * )
     *
     * @OA\Schema(
     *     schema="ErrorUnauthenticated",
     *     type="object",
     *     @OA\Property(property="message", type="string", example="Unauthenticated")
     * )
     *
     * @OA\Schema(
     *     schema="ErrorTaskUpdateValidation",
     *     type="object",
     *     @OA\Property(property="message", type="string", example="Task title is required (and 3 more errors)"),
     *     @OA\Property(property="errors", type="object",
     *         @OA\Property(property="parent", type="array", description="Array of error messages related to parent task field",
     *              @OA\Items(
     *                   type="string",
     *                   example="Parent task not found."
     *              )
     *         ),
     *         @OA\Property(property="title", type="array", description="Array of error messages related to title field",
     *              @OA\Items(
     *                   type="string",
     *                   example="Task title is required."
     *              )
     *         ),
     *         @OA\Property(property="status", type="array", description="Array of error messages related to status field",
     *              @OA\Items(
     *                   type="string",
     *                   example="Task status is required."
     *              )
     *         ),
     *         @OA\Property(property="priority", type="array", description="Array of error messages related to priority field",
     *              @OA\Items(
     *                   type="string",
     *                   example="Task priority is required."
     *              )
     *         ),
     *         @OA\Property(property="details", type="array", description="Array of error messages related to details field",
     *              @OA\Items(
     *                   type="string",
     *                   example="Task details is required."
     *              )
     *         )
     *     )
     * )
     *
     * @OA\Schema(
     *      schema="ErrorTaskCreateValidation",
     *      type="object",
     *      @OA\Property(property="message", type="string", example="Task project is required (and 4 more errors)"),
     *      @OA\Property(property="errors", type="object",
     *          @OA\Property(property="project", type="array", description="Array of error messages related to project field",
     *              @OA\Items(
     *                  type="string",
     *                  example="Project is required."
     *              )
     *          ),
     *          @OA\Property(property="parent", type="array", description="Array of error messages related to parent task field",
     *              @OA\Items(
     *                  type="string",
     *                  example="Parent task not found."
     *              )
     *          ),
     *          @OA\Property(property="title", type="array", description="Array of error messages related to title field",
     *              @OA\Items(
     *                  type="string",
     *                  example="Task title is required."
     *              )
     *          ),
     *          @OA\Property(property="status", type="array", description="Array of error messages related to status field",
     *              @OA\Items(
     *                  type="string",
     *                  example="Task status is required."
     *              )
     *          ),
     *          @OA\Property(property="priority", type="array", description="Array of error messages related to priority field",
     *              @OA\Items(
     *                  type="string",
     *                  example="Task priority is required."
     *              )
     *          ),
     *          @OA\Property(property="details", type="array", description="Array of error messages related to details field",
     *              @OA\Items(
     *                  type="string",
     *                  example="Task details is required."
     *              )
     *          )
     *      )
     * )
     *
     * @OA\Schema(
     *      schema="SuccessTaskCreated",
     *      type="object",
     *      @OA\Property(property="message", type="string", example="Task created"),
     *      @OA\Property(property="success", type="object",
     *          @OA\Property(property="task", type="object", ref="#/components/schemas/Task")
     *      )
     * )
     *
     * @OA\Schema(
     *      schema="SuccessTaskUpdated",
     *      type="object",
     *      @OA\Property(property="message", type="string", example="Task updated"),
     *      @OA\Property(property="success", type="object",
     *          @OA\Property(property="task", type="object",
     *              @OA\Property(property="old", type="object", ref="#/components/schemas/Task", description="Old Task"),
     *              @OA\Property(property="new", type="object", ref="#/components/schemas/Task", description="Updated Task"),
     *          )
     *      )
     * )
     *
     * @OA\Schema(
     *      schema="TaskCreateRequest",
     *      type="object",
     *      required={"project", "title", "status", "priority", "details", "deadline"},
     *      @OA\Property(property="project", type="string", example="01j99vx286z90vhmebjnj7rycw"),
     *      @OA\Property(property="parent", type="string", example="01j9ad21w8dzjp5tktp28z3qef", nullable=true),
     *      @OA\Property(property="title", type="string", example="Sample Task"),
     *      @OA\Property(property="status", type="integer", example=1),
     *      @OA\Property(property="priority", type="integer", example=2),
     *      @OA\Property(property="deadline", type="string", example="2024-10-05 02:59:00", format="date-time"),
     *      @OA\Property(property="details", type="string", example="Task details")
     * )
     *
     * @OA\Schema(
     *      schema="TaskUpdateRequest",
     *      type="object",
     *      required={"title", "status", "priority", "details", "deadline"},
     *      @OA\Property(property="parent", type="string", example="01j9ad21w8dzjp5tktp28z3qef", nullable=true),
     *      @OA\Property(property="title", type="string", example="Sample Task"),
     *      @OA\Property(property="status", type="integer", example=1),
     *      @OA\Property(property="priority", type="integer", example=2),
     *      @OA\Property(property="deadline", type="string", example="2024-10-05 02:59:00", format="date-time"),
     *      @OA\Property(property="details", type="string", example="Task details")
     * )
     *
     * @OA\Schema(
     *      schema="ErrorTaskNotFound",
     *      type="object",
     *      @OA\Property(property="message", type="string", example="Task not found"),
     *      @OA\Property(property="errors", type="object",
     *          @OA\Property(property="task", type="object", example="Bad request or task not exists")
     *      )
     * )
     *
     * @OA\Schema(
     *      schema="SuccessTaskDeleted",
     *      type="object",
     *      @OA\Property(property="message", type="string", example="Task deleted"),
     *      @OA\Property(property="success", type="object",
     *          @OA\Property(property="task", type="object",
     *              @OA\Property(property="deleted", type="object", ref="#/components/schemas/Task", description="Deleted Task"),
     *          )
     *      )
     * )
     *
     * @OA\Schema(
     *      schema="SuccessTaskFetch",
     *      type="object",
     *      @OA\Property(property="message", type="string", example="Task fetched"),
     *      @OA\Property(property="success", type="object",
     *          @OA\Property(property="task", type="object", ref="#/components/schemas/Task")
     *      )
     * )
     *
     * @OA\Schema(
     *      schema="SuccessTasksPaginated",
     *      type="object",
     *      @OA\Property(property="data", type="array",
     *          @OA\Items(ref="#/components/schemas/Task")
     *      ),
     *      @OA\Property(property="pagination", type="object",
     *          @OA\Property(property="total", type="integer"),
     *          @OA\Property(property="per_page", type="integer"),
     *          @OA\Property(property="current_page", type="integer"),
     *          @OA\Property(property="last_page", type="integer"),
     *          @OA\Property(property="from", type="integer"),
     *          @OA\Property(property="to", type="integer"),
     *          @OA\Property(property="first", type="string"),
     *          @OA\Property(property="last", type="string"),
     *          @OA\Property(property="next", type="string"),
     *          @OA\Property(property="prev", type="string")
     *      )
     * )
     *
     * @OA\Schema(
     *      schema="SuccessProjectPaginated",
     *      type="object",
     *      @OA\Property(property="data", type="array",
     *          @OA\Items(ref="#/components/schemas/Project")
     *      ),
     *      @OA\Property(property="pagination", type="object",
     *          @OA\Property(property="total", type="integer"),
     *          @OA\Property(property="per_page", type="integer"),
     *          @OA\Property(property="current_page", type="integer"),
     *          @OA\Property(property="last_page", type="integer"),
     *          @OA\Property(property="from", type="integer"),
     *          @OA\Property(property="to", type="integer"),
     *          @OA\Property(property="first", type="string"),
     *          @OA\Property(property="last", type="string"),
     *          @OA\Property(property="next", type="string"),
     *          @OA\Property(property="prev", type="string")
     *      )
     * )
     *
     * @OA\Schema(
     *      schema="SuccessProjectFetch",
     *      type="object",
     *      @OA\Property(property="message", type="string", example="Project fetched"),
     *      @OA\Property(property="success", type="object",
     *          @OA\Property(property="project", type="object", ref="#/components/schemas/Project")
     *      )
     * )
     *
     * @OA\Schema(
     *      schema="ErrorProjectNotFound",
     *      type="object",
     *      @OA\Property(property="message", type="string", example="Project not found"),
     *      @OA\Property(property="errors", type="object",
     *          @OA\Property(property="project", type="object", example="Bad request or project not exists")
     *      )
     * )
     *
     * @OA\Schema(
     *      schema="ProjectRequest",
     *      type="object",
     *      required={"title", "status", "priority", "description", "deadline"},
     *      @OA\Property(property="title", type="string", example="Sample Project"),
     *      @OA\Property(property="status", type="integer", example=1),
     *      @OA\Property(property="priority", type="integer", example=2),
     *      @OA\Property(property="deadline", type="string", example="2024-10-05 02:59:00", format="date-time"),
     *      @OA\Property(property="description", type="string", example="Project description")
     * )
     *
     * @OA\Schema(
     *      schema="SuccessProjectCreated",
     *      type="object",
     *      @OA\Property(property="message", type="string", example="Project created"),
     *      @OA\Property(property="success", type="object",
     *          @OA\Property(property="project", type="object", ref="#/components/schemas/Project")
     *      )
     * )
     *
     * @OA\Schema(
     *     schema="ErrorProjectValidation",
     *     type="object",
     *     @OA\Property(property="message", type="string", example="Project title is required (and 3 more errors)"),
     *     @OA\Property(property="errors", type="object",
     *         @OA\Property(property="title", type="array", description="Array of error messages related to title field",
     *              @OA\Items(
     *                   type="string",
     *                   example="Project title is required."
     *              )
     *         ),
     *         @OA\Property(property="status", type="array", description="Array of error messages related to status field",
     *              @OA\Items(
     *                   type="string",
     *                   example="Project status is required."
     *              )
     *         ),
     *         @OA\Property(property="priority", type="array", description="Array of error messages related to priority field",
     *              @OA\Items(
     *                   type="string",
     *                   example="Project priority is required."
     *              )
     *         ),
     *         @OA\Property(property="description", type="array", description="Array of error messages related to description field",
     *              @OA\Items(
     *                   type="string",
     *                   example="Project description is required."
     *              )
     *         )
     *     )
     * )
     *
     * @OA\Schema(
     *      schema="SuccessProjectUpdated",
     *      type="object",
     *      @OA\Property(property="message", type="string", example="Project updated"),
     *      @OA\Property(property="success", type="object",
     *          @OA\Property(property="project", type="object",
     *              @OA\Property(property="old", type="object", ref="#/components/schemas/Project", description="Old Project"),
     *              @OA\Property(property="new", type="object", ref="#/components/schemas/Project", description="Updated Project")
     *          )
     *      )
     * )
     *
     * @OA\Schema(
     *      schema="SuccessProjectDeleted",
     *      type="object",
     *      @OA\Property(property="message", type="string", example="Project deleted"),
     *      @OA\Property(property="success", type="object",
     *          @OA\Property(property="project", type="object",
     *              @OA\Property(property="deleted", type="object", ref="#/components/schemas/Project", description="Deleted Project")
     *          )
     *      )
     * )
     *
     * @OA\Schema(
     *      schema="LoginRequest",
     *      type="object",
     *      required={"email", "password"},
     *      @OA\Property(property="email", type="string", example="user@localhost"),
     *      @OA\Property(property="password", type="string", example="password")
     * )
     *
     * @OA\Schema(
     *      schema="RegisterRequest",
     *      type="object",
     *      required={"name", "email", "password", "password_confirmation"},
     *      @OA\Property(property="name", type="string", example="User"),
     *      @OA\Property(property="email", type="string", example="user@localhost"),
     *      @OA\Property(property="password", type="string", example="password"),
     *      @OA\Property(property="password_confirmation", type="string", example="password")
     * )
     *
     * @OA\Schema(
     *     schema="ErrorRegisterValidation",
     *     type="object",
     *     @OA\Property(property="message", type="string", example="The name field is required (and 2 more errors)"),
     *     @OA\Property(property="errors", type="object",
     *         @OA\Property(property="name", type="array", description="Array of error messages related to name field",
     *              @OA\Items(
     *                   type="string",
     *                   example="The name field is required."
     *              )
     *         ),
     *         @OA\Property(property="email", type="array", description="Array of error messages related to email field",
     *              @OA\Items(
     *                   type="string",
     *                   example="The email field is required."
     *              )
     *         ),
     *         @OA\Property(property="password", type="array", description="Array of error messages related to password field",
     *              @OA\Items(
     *                   type="string",
     *                   example="The password field is required."
     *              )
     *         )
     *     )
     * )
     *
     * @OA\Schema(
     *     schema="ErrorLoginValidation",
     *     type="object",
     *     @OA\Property(property="message", type="string", example="The email field is required (and 1 more errors)"),
     *     @OA\Property(property="errors", type="object",
     *         @OA\Property(property="email", type="array", description="Array of error messages related to email field",
     *              @OA\Items(
     *                   type="string",
     *                   example="The email field is required."
     *              )
     *         ),
     *         @OA\Property(property="password", type="array", description="Array of error messages related to password field",
     *              @OA\Items(
     *                   type="string",
     *                   example="The password field is required."
     *              )
     *         )
     *     )
     * )
     *
     * @OA\Schema(
     *      schema="User",
     *      type="object",
     *      @OA\Property(property="id", type="integer", example=1),
     *      @OA\Property(property="name", type="string", example="User"),
     *      @OA\Property(property="email", type="string", example="user@localhost"),
     *      @OA\Property(property="email_verified_at", type="string", format="date-time", example="2024-10-03T15:26:34.000000Z", nullable=true),
     *      @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-03T15:26:34.000000Z"),
     *      @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-03T15:26:34.000000Z")
     * )
     *
     * @OA\Schema(
     *      schema="SuccessAuth",
     *      type="object",
     *      @OA\Property(property="message", type="string", example="Authenticated as User"),
     *      @OA\Property(property="token", type="string", example="JWT Token"),
     *      @OA\Property(property="user", type="object", ref="#/components/schemas/User")
     * )
     *
     * @OA\SecurityScheme(
     *     securityScheme="JwtToken",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     description="Enter your JWT token"
     * )
     */
    public function boot(): void
    {
        Validator::extend('word_count', function ($attribute, $value, $parameters, $validator) {
            return (new WordCount(...$parameters))->validate($attribute, $value, function ($message) use ($validator, $attribute) {
                $validator->errors()->add($attribute, $message);
            });
        });

        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
