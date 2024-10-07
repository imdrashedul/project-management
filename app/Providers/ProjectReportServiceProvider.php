<?php

namespace App\Providers;

use App\Models\Project;
use App\Services\ProjectReportService;
use App\Services\ProjectService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Enums\TaskStatus;

class ProjectReportServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ProjectReportService::class, function ($app) {
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
                ])->with("tasks")->where($model->getRouteKeyName(), $project)->first() : $project
            ) : $project;
            //
            return new ProjectReportService($project ?? app(Project::class));
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
