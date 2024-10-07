<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Task;
use App\Models\Project;


return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(Task::table(), function (Blueprint $table) {
            //Primary Key (auto-incrementing) (for indexing and relations)
            $table->id();

            //Secondary Key ulid (for public interactions)
            $table->ulid()->unique();

            //Foreign Key: referring to the project to which this task belongs.
            $table->foreignIdFor(Project::class, "project_id")
                ->constrained(Project::table())
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            //Self referenced foreign key for sub and parent task.
            $table->foreignIdFor(Task::class, "parent_id")->nullable()
                ->constrained(Task::table())
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            //task Title
            $table->string("title", 255); //Max 255 Char Length

            //task State / Status
            $table->unsignedTinyInteger("status")
                ->default(0) // Initiation
                ->comment("Follows Enum:TaskStatus");

            //Task Priority
            $table->unsignedTinyInteger("priority")
                ->default(2) // Medium
                ->comment("Follows Enum:Priority");

            //Task Details
            $table->longText("details");

            //Task Deadline
            $table->dateTime("deadline")->nullable();

            //Trash
            $table->softDeletes();

            //Create and Update Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Task::table());
    }
};
