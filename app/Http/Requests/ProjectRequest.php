<?php

namespace App\Http\Requests;

use App\Rules\WordCount;
use App\Traits\Helpers\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Priority;
use App\Enums\ProjectStatus;

class ProjectRequest extends FormRequest
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
            "status" => ["required", "integer", "min:0", "max:255", $this->rule_enum(ProjectStatus::class)],
            "priority" => ["required", "integer", "min:0", "max:255", $this->rule_enum(Priority::class)],
            "description" => ["required", "string", new WordCount(10)],
            "deadline" => ["required", "date_format:Y-m-d H:i:s"]
        ];
    }

    /**
     * Summary of messages
     * @return array
     */
    public function messages(): array
    {
        return [
            //Title
            "title.required" => "Project title is required",
            "title.string" => "Title must be a string",
            "title.max" => "Title must be not more than 255 characters long",

            //Status
            "status.required" => "Project status is required",
            "status.integer" => "Project status must be integer",
            "status.min" => "Status must be greater than or equal to 0",
            "status.max" => "Status must be less than or equal to 255",
            "status.enum" => "Invalid status or not exists",

            //Status
            "priority.required" => "Project priority is required",
            "priority.integer" => "Priority must be integer",
            "priority.min" => "Priority must be greater than or equal to 0",
            "priority.max" => "Priority must be less than or equal to 255",
            "priority.enum" => "Invalid priority or not exists",

            //Details
            "description.required" => "Project description is required",
            "description.string" => "Invalid project description",
            "description.word_count" => "Project description must be more than 10 words.",

            //Deadline
            "deadline.date_format" => "Invalid project deadline"
        ];
    }
}
