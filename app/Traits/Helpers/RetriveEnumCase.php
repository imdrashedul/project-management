<?php

namespace App\Traits\Helpers;
use Illuminate\Support\Collection;

trait RetriveEnumCase
{
    /**
     * @return string
     */
    public function case(): string
    {
        // Convert the case name to a readable key
        // Add space between words
        $key = preg_replace('/([a-z])([A-Z])/', '$1 $2', $this->name);
        // Capitalize first letters of each word
        return ucwords(trim($key));
    }
}
