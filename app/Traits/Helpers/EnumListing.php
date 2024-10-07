<?php

namespace App\Traits\Helpers;
use Illuminate\Support\Collection;

trait EnumListing
{
    /**
     * @return array
     */
    public static function caseValueList(): array
    {
        return Collection::make(self::cases())->reduce(function ($carry, $status) {
            // Convert the case name to a readable key
            // Add space between words
            $key = preg_replace('/([a-z])([A-Z])/', '$1 $2', $status->name);
            // Capitalize first letters of each word
            $key = ucwords(trim($key));
            $carry[$key] = $status->value;
            return $carry;
        }, []);
    }

    /**
     * @return array
     */
    public static function valueCaseList(): array
    {
        return Collection::make(self::cases())->reduce(function ($carry, $status) {
            // Convert the case name to a readable key
            // Add space between words
            $key = preg_replace('/([a-z])([A-Z])/', '$1 $2', $status->name);
            // Capitalize first letters of each word
            $key = ucwords(trim($key));
            $carry[$status->value] = $key;
            return $carry;
        }, []);
    }
}
