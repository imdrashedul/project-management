<?php

namespace App\Http\Requests;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Traits\Helpers\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\WordCount;

class TaskRequest extends FormRequest
{
    use ValidationRules;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "title" => ["required", "string", "max:255"],
            "project" => ["required", "string", $this->rule_exists(Project::class)],
            "parent" => ["bail", "nullable", "string", $this->rule_exists(Task::class)],
            "status" => ["required", "integer", "min:0", "max:255", $this->rule_enum(TaskStatus::class)],
            "priority" => ["required", "integer", "min:0", "max:255", $this->rule_enum(Priority::class)],
            "details" => ["required", "string", new WordCount(20)],
            "deadline" => ["required", "date_format:Y-m-d H:i:s"]
        ];
    }

    public function messages(): array
    {
        return [
            //Title
            "title.required" => "Task title is required",
            "title.string" => "Title must be a string",
            "title.max" => "Title must be not more than 255 characters long",

            //Project
            "project.required" => "The project to which the task belongs is required",
            "project.string" => "Invalid project id",
            "project.exists" => "Project not exists",

            //Parent task
            "parent.string" => "Invalid parent task id",
            "parent.exists" => "Parent task not exists",

            //Status
            "status.required" => "Task status is required",
            "status.integer" => "Task status must be integer",
            "status.min" => "Status must be greater than or equal to 0",
            "status.max" => "Status must be less than or equal to 255",
            "status.enum" => "Invalid status or not exists",

            //Status
            "priority.required" => "Task priority is required",
            "priority.integer" => "Priority must be integer",
            "priority.min" => "Priority must be greater than or equal to 0",
            "priority.max" => "Priority must be less than or equal to 255",
            "priority.enum" => "Invalid priority or not exists",

            //Details
            "details.required" => "Task details is required",
            "details.string" => "Invalid task details",
            "details.word_count" => "Task details must be more than 20 words.",

            //Deadline
            "deadline.date_format" => "Invalid task deadline"
        ];
    }
}
