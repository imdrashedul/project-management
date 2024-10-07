<?php

namespace App\Models;

use App\Enums\TaskStatus;
use App\Enums\Priority;
use App\Traits\ModelTableResolver;
use App\Traits\HasSecondaryUlid;
use App\Traits\Helpers\CastingRules;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Task extends Model
{
    use HasFactory,
        SoftDeletes,

        // Set static accessor of the table name for this model
        ModelTableResolver,

        // Enable secondary ulid support for this model
        HasSecondaryUlid,

        // Add custom custing rule support to this model
        CastingRules;

    /**
     * Mass-assignable attributes
     *
     * @var array
     */
    protected $fillable = ["title", "status", "priority", "parent_id", "project_id", "details", "deadline"];

    /**
     * Default item limit per page
     * @var int
     */
    protected $perPage = 15;

    /**
     * Casting behaviours for fields.
     * @return array
     */
    protected function casts(): array
    {
        return [
            "title" => "string",
            "status" => $this->cast_enum(TaskStatus::class),
            "priority" => $this->cast_enum(Priority::class),
            "deadline" => "datetime",
            "created_at" => "datetime",
            "updated_at" => "datetime"
        ];
    }

    /**
     * Task subtasks
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, "parent_id");
    }

    /**
     * Parent Task
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, "parent_id");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Format deadline datetime to database datetime string
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function deadlineFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->deadline->format("Y-m-d H:i:s")
        );
    }

    /**
     * Set route key
     * @return string
     */
    public function getRouteKey(): string
    {
        return "task";
    }

    public function subtaskWithSubtasks(): Collection
    {
        return $this->subtasks()->withCount("subtasks as subtask_count")->withCount([
            "subtasks as pending_subtask_count" => function ($query) {
                $query->whereIn("status", [
                    TaskStatus::InProgress,
                    TaskStatus::InReview,
                    TaskStatus::Assigned,
                    TaskStatus::NotStarted
                ]);
            }
        ])->with(["parent", "project"])->orderByRaw(
                sprintf("FIELD(`priority`, %s)", implode(",", [
                    Priority::Critical->value,
                    Priority::High->value,
                    Priority::Medium->value,
                    Priority::Low->value,
                    Priority::Optional->value
                ]))
            )->orderBy("deadline")->get();
    }

    public function paginatedSubtasks($perPage = null): LengthAwarePaginator
    {
        return $this->subtasks()->withCount("subtasks as subtask_count")->withCount([
            "subtasks as pending_subtask_count" => function ($query) {
                $query->whereIn("status", [
                    TaskStatus::InProgress,
                    TaskStatus::InReview,
                    TaskStatus::Assigned,
                    TaskStatus::NotStarted
                ]);
            }
        ])->with(["parent", "project"])->orderByRaw(
                sprintf("FIELD(`priority`, %s)", implode(",", [
                    Priority::Critical->value,
                    Priority::High->value,
                    Priority::Medium->value,
                    Priority::Low->value,
                    Priority::Optional->value
                ]))
            )->orderBy("deadline")
            ->paginate($perPage);
    }

    protected static function booted()
    {
        static::deleting(function ($task) {
            if (!$task->isForceDeleting()) {
                $task->subtasks()->whereNull("deleted_at")->delete();
            }
        });

        static::restoring(function ($task) {
            $pda = $task->deleted_at;

            $task->subtasks()->withTrashed()
                ->where("deleted_at", ">=", $pda)
                ->restore();
        });
    }
}
