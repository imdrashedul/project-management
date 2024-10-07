<?php

namespace App\Providers;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Services\ProjectService;
use App\Services\TaskService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class TaskServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(TaskService::class, function ($app) {
            // Automatically resolve the Task model from the route
            $model = app(Task::class);
            //
            $task = Route::current()->parameter($model->getRouteKey());
            $task = !empty($task) ? (
                !($task instanceof Task) ? $model->where($model->getRouteKeyName(), $task)->withCount("subtasks as subtask_count")->withCount([
                    "subtasks as pending_subtask_count" => function ($query) {
                        $query->whereIn("status", [
                            TaskStatus::InProgress,
                            TaskStatus::InReview,
                            TaskStatus::Assigned,
                            TaskStatus::NotStarted
                        ]);
                    }
                ])->first() : $task
            ) : $task;
            //
            return new TaskService(
                app(ProjectService::class),
                $task ?? app(Task::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
