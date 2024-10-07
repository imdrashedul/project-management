<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Resources\ApiCollection;
use Illuminate\Support\Facades\Route;

Route::prefix("v1")->middleware("api")->group(function () {
    Route::prefix("auth")->controller(AuthController::class)->as("api.auth.")->group(function () {
        Route::post("login", "login")->name("login");
        Route::post("register", "register")->name("register");
    });

    Route::middleware("auth:api")->group(function () {
        Route::apiResource("projects", ProjectController::class);
        Route::apiResource("tasks", TaskController::class);

        Route::prefix("projects")
            ->as("projects.")
            ->controller(ProjectController::class)
            ->group(function () {
                Route::get("{project}/tasks", "tasks")->name("tasks");
            });

        Route::prefix("tasks")
            ->as("tasks.")
            ->controller(TaskController::class)
            ->group(function () {
                Route::get("{task}/subtasks", "subtasks")->name("subtasks");
            });
    });

    Route::fallback(function () {
        return (new ApiCollection("End-point not found or method not supported"))->errors([
            "request" => "Bad Request"
        ]);
    });
});



