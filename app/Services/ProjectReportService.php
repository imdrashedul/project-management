<?php

namespace App\Services;
use App\Traits\Helpers\FallbackResolver;
use App\Models\Project;

class ProjectReportService
{
    use FallbackResolver;
    /**
     * @param \App\Models\Project $project
     */
    public function __construct(private Project $project)
    {
        // Empty Space Isn't Empty :)
    }

    /**
     * Used to redirect if null model for update, delete, show.
     * @return mixed
     */
    public function fallbackIfRequired(): mixed
    {
        return $this->resolveFallback($this->isEmptyProject());
    }

    /**
     * Determine if project instance is empty or not
     * @return bool
     */
    public function isEmptyProject(): bool
    {
        return !$this->project->exists && empty($this->project->getAttributes());
    }

    /**
     * @return \App\Models\Project
     */
    public function project(): Project
    {
        return $this->project;
    }
}
