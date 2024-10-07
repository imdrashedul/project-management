<?php

namespace App\Enums;
use App\Traits\Helpers\EnumListing;
use App\Traits\Helpers\RetriveEnumCase;

enum ProjectStatus: int
{
    use EnumListing, RetriveEnumCase;
    /**
     * Project vision, goals, and objectives.
     */
    case Initiation = 0;

    /**
     * Detailed project plan, including scope, schedule, and resources.
     */
    case Planning = 1;

    /**
     * Analyzing and prioritizing requirements
     */
    case Analysis = 2;

    /**
     * Architecture, UI/UX design
     */
    case Design = 3;

    /**
     * Start coding and implementing the designs, code reviews.
     * Integrating components and developing the full system.
     */
    case Development = 4;

    /**
     * Performing Unit Testing, Integration Testing, System Testing, UAT
     */
    case Testing = 5;

    /**
     * Deploying the system to a production environment.
     */
    case Release = 6;

    /**
     * Ongoing support to fix any post-release issues
     */
    case Support = 7;

    /**
     * Conducting post-project evaluation. (lessons learned, post-mortem analysis)
     */
    case Evaluation = 8;

    /**
     *
     */
    case Retirement = 9;
}
