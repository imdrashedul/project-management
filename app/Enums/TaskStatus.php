<?php

namespace App\Enums;
use App\Traits\Helpers\EnumListing;
use App\Traits\Helpers\RetriveEnumCase;

enum TaskStatus: int
{
    use EnumListing, RetriveEnumCase;
    /**
     * Task added but not assigned to start work
     */
    case NotAssigned = 0;

    /**
     * Task marked to start working on it
     */
    case Assigned = 1;

    /**
     * Task marked to start but not started
     */
    case NotStarted = 2;

    /**
     * Task is in progress
     */
    case InProgress = 3;

    /**
     * Task has been temporarily paused.
     */
    case Paused = 4;

    /**
     * The task is fully completed, and approval is needed.
     */
    case Completed = 5;

    /**
     * The task has been completed, but it's under review for approval or quality assurance.
     */
    case InReview = 6;

    /**
     * The task has been reviewed and approved by stakeholders or relevant authorities.
     */
    case Approved = 7;
    /**
     * The task was completed but did not meet the required standards or needs.
     */
    case Rejected = 8;

    /**
     * The task has been cancelled due to any factor.
     */
    case Cancelled = 9;

    /**
     * The task is fully completed, approved, and no further action is required.
     */
    case Closed = 10;
}
