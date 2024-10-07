<?php

namespace App\Enums;
use App\Traits\Helpers\EnumListing;
use App\Traits\Helpers\RetriveEnumCase;

enum Priority: int
{
    use EnumListing, RetriveEnumCase;
    /**
     * Tasks that are not necessary but would add value or improve the project.
     * Examples: Extra features, "nice-to-have" design enhancements, optional refinements.
     */
    case Optional = 0;

    /**
     * Tasks that have minimal impact or can be deferred without immediate consequences.
     * Examples: Minor improvements, cosmetic changes, long-term planning.
     */
    case Low = 1;

    /**
     * Tasks that are important but not time-sensitive or urgent.
     * Examples: Feature enhancements, non-critical bug fixes, optimizations.
     */
    case Medium = 2;

    /**
     * Important tasks that need to be completed soon but may not be immediate emergencies.
     * Examples: Key milestones, important client deliverables, high-visibility bugs or issues.
     */
    case High = 3;

    /**
     * Must be addressed immediately to avoid project failure or major consequences.
     * Examples: System outages, major security vulnerabilities, deadlines for high-impact deliverables.
     */
    case Critical = 4;
}
