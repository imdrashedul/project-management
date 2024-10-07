<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

/**
 * Prefix: /tasks
 * As: tasks.
 * Controller: Task
 */
// Select2 End-Point
Route::get("/select2", "select2")->name("select2");
Route::get("/select2/single", "select2single")->name("select2.single");

Route::get("/", TaskController::class)->name("index");
Route::get("/create/{project?}", "create")->name("create");
Route::get("/create/{task?}/task", "createByTask")->name("create.task");
Route::post("/", "store")->name("store");
Route::get("/{task}", "show")->name("show");
Route::get("/{task}/edit", "edit")->name("edit");
Route::put("/{task}", "update")->name("update");
Route::delete("/{task}", "destroy")->name("destroy");
