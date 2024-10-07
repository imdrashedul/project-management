<?php
namespace App\Traits\Helpers;
use App\Casts\EnumCast;

trait CastingRules
{
    /**
     * Cast a value to given enum using EnumCast
     * @param string $enum
     * @return string
     */
    protected function cast_enum(string $enum): string
    {
        return sprintf("%s:%s", EnumCast::class, $enum);
    }
}
