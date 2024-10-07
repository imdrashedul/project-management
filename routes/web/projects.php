<?php
use App\Http\Controllers\ProjectReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

/**
 * Prefix: /projects
 * As: projects.
 * Controller: Project
 */
// Select2 End-Point
Route::get("/select2", "select2")->name("select2");
Route::get("/select2/single", "select2single")->name("select2.single");

Route::get("/import", "upload")->name("upload");
Route::post("/import", "import")->name("import");

Route::get("/", ProjectController::class)->name("index");
Route::get("/create", "create")->name("create");
Route::post("/", "store")->name("store");
Route::get("/{project}", "show")->name("show");
Route::get("/{project}/edit", "edit")->name("edit");
Route::put("/{project}", "update")->name("update");
Route::delete("/{project}", "destroy")->name("destroy");

Route::get("/{project}/report", ProjectReportController::class)->name("report");


