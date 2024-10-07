<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Traits\Helpers\CastingRules;
use App\Traits\ModelTableResolver;
use App\Traits\HasSecondaryUlid;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Project extends Model
{
    use HasFactory,
        SoftDeletes,

        // Set static accessor of the table name for this model
        ModelTableResolver,

        // Enable secondary ulid support for this model
        HasSecondaryUlid,

        // Add custom casting rules support to this model
        CastingRules;

    /**
     * Mass-assignable attributes
     * @var array
     */
    protected $fillable = ["title", "status", "priority", "user_id", "description", "deadline"];

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
            "status" => $this->cast_enum(ProjectStatus::class),
            "priority" => $this->cast_enum(Priority::class),
            "deadline" => "datetime",
            "created_at" => "datetime",
            "updated_at" => "datetime"
        ];
    }

    /**
     * Project tasks
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Project creator
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
        return "project";
    }

    /**
     * Tasks Paginated Collection
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginatedTasks($perPage = null): LengthAwarePaginator
    {
        return $this->tasks()->whereNull("parent_id")->withCount("subtasks as subtask_count")->withCount([
            "subtasks as pending_subtask_count" => function ($query) {
                $query->whereIn("status", [
                    TaskStatus::InProgress,
                    TaskStatus::InReview,
                    TaskStatus::Assigned,
                    TaskStatus::NotStarted
                ]);
            }
        ])->orderByRaw(
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
        static::deleting(function ($project) {
            if (!$project->isForceDeleting()) {
                $project->tasks()->whereNull("deleted_at")->delete();
            }
        });

        static::restoring(function ($project) {
            $pda = $project->deleted_at;

            $project->tasks()->withTrashed()
                ->where("deleted_at", ">=", $pda)
                ->restore();
        });
    }
}
