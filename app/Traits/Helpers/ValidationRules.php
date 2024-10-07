<?php

namespace App\Traits\Helpers;

use Illuminate\Validation\Rules\Enum;

trait ValidationRules
{
    /**
     * Generates exists rule for request validation from model and column
     * @param string $model
     * @param string $column
     * @return string
     */
    protected function rule_exists(string $model, string $column = "ulid"): string
    {
        return sprintf("exists:%s,%s", $model::table(), $column);
    }

    /**
     * Rule to validate if a value exists in an enum using the Enum rule
     * @param string $enum
     * @return \Illuminate\Validation\Rules\Enum
     */
    protected function rule_enum(string $enum): Enum
    {
        return new Enum($enum);
    }
}
