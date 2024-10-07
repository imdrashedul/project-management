<?php

use App\Http\Controllers\CsvImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

/**
 * Route to redirect to the initial page based on authentication.
 * If unauthorized, it will be redirected to the login route.
 * Otherwise, it will navigate to the tasks route.
 * */
Route::redirect('/', '/tasks');

/**
 * Group of routes that should be accessible only to authorized and verified users.
 */
Route::middleware(['auth', 'verified'])->group(function () {

    Route::prefix("projects")
        ->as("projects.")
        ->controller(ProjectController::class)
        ->group(base_path("routes/web/projects.php"));

    Route::prefix("tasks")
        ->as("tasks.")
        ->controller(TaskController::class)
        ->group(base_path("routes/web/tasks.php"));

    Route::prefix("import")
        ->as("import.")
        ->controller(CsvImportController::class)
        ->group(function () {
            Route::get("/", "upload")->name("upload");
            Route::post("/", "process")->name("process");
        });
});


/**
 * Laravel Breeze generated user profile routes.
 */
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


/**
 * This file contains Laravel Breeze generated auth routes.
 */
require __DIR__ . '/auth.php';
require __DIR__ . '/channels.php';
