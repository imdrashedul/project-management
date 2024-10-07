<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Traits\Helpers\ValidationRules;
use App\Rules\WordCount;

class TaskUpdateRequest extends FormRequest
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
            "parent" => ["bail", "nullable", "string", $this->rule_exists(Task::class)],
            "status" => ["required", "integer", "min:0", "max:255", $this->rule_enum(TaskStatus::class)],
            "priority" => ["required", "integer", "min:0", "max:255", $this->rule_enum(Priority::class)],
            "details" => ["required", "string", new WordCount(20)],
            "deadline" => ["required", "date_format:Y-m-d H:i:s"]
        ];
    }
}
