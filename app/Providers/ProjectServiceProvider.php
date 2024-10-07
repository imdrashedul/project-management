<?php

namespace App\Providers;

use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Enums\TaskStatus;

class ProjectServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ProjectService::class, function ($app) {
            // Automatically resolve the Project model from the route
            $model = app(Project::class);
            //
            $project = Route::current()->parameter($model->getRouteKey());
            $project = !empty($project) ? (
                !($project instanceof Project) ? $model->withCount([
                    "tasks as task_count" => function ($query) {
                        $query->whereNull("parent_id");
                    },
                    "tasks as pending_task_count" => function ($query) {
                        $query->whereNull("parent_id")->whereIn("status", [
                            TaskStatus::InProgress,
                            TaskStatus::InReview,
                            TaskStatus::Assigned,
                            TaskStatus::NotStarted
                        ]);
                    }
                ])->where($model->getRouteKeyName(), $project)->first() : $project
            ) : $project;
            //
            return new ProjectService($project ?? app(Project::class));
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
