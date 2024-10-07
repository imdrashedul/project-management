<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->ulid,
            "title" => $this->title,
            "status" => $this->status->case(),
            "status_code" => $this->status->value,
            "priority" => $this->priority->case(),
            "priority_code" => $this->priority->value,
            "details" => $this->details,
            "deadline" => $this->deadline,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "subtask_count" => $this->subtask_count,
            "pending_subtask_count" => $this->pending_subtask_count,
            "project" => new ProjectResource($this->project),
            "parent" => !empty($this->parent_id) ? new ParentTaskResource($this->parent) : null
        ];
    }
}
