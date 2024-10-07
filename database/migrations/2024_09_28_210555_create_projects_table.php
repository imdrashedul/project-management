<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Project;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(Project::table(), function (Blueprint $table) {
            //Primary Key (auto-incrementing) (for indexing and relations)
            $table->id();

            //Secondary Key ulid (for public interactions)
            $table->ulid()->unique();

            //Project Title
            $table->string("title", 255); //Max 255 Char Length

            //Project State / Status
            $table->unsignedTinyInteger("status")
                ->default(0) // Initiation
                ->comment("Follows Enum:ProjectStatus");

            //Project Priority
            $table->unsignedTinyInteger("priority")
                ->default(2) // Medium
                ->comment("Follows Enum:Priority");

            //Project Creator
            $table->unsignedBigInteger("user_id")->comment("Foreign Users->id");

            //Project Description
            $table->text("description");

            //Project Deadline
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
        Schema::dropIfExists(Project::table());
    }
};
