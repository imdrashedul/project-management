<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class WordCount implements ValidationRule
{
    protected $min;
    protected $max;

    public function __construct(int $min = null, int $max = null)
    {
        $this->min = $min ?? 0;
        $this->max = $max ?? PHP_INT_MAX;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $wordCount = Str::of($value)->explode(" ")->count();

        if ($wordCount < $this->min) {
            $fail("The {$attribute} must have at least {$this->min} words");
        }

        if ($wordCount > $this->max) {
            $fail("The {$attribute} may not have more than {$this->max} words");
        }
    }
}
