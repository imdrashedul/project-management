<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HasSecondaryUlid
{
    /**
     * Initialize the event listener and when creating model set new unique ulid to the model.
     *
     * @return void
     */
    protected static function bootHasSecondaryUlid()
    {
        static::creating(function ($model) {
            if (empty($model->ulid)) {
                $model->ulid = strtolower((string) Str::ulid());
            }
        });
    }

    /**
     * Initialize and add the cast rule of ulid for string conversation if required.
     * @return void
     */
    public function initializeHasSecondaryUlid()
    {
        $this->mergeCasts([
            "ulid" => "string"
        ]);
    }

    /**
     * Provides unique ids
     * @return string[]
     */
    public function uniqueIds()
    {
        return [$this->getKeyName(), "ulid"];
    }

    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        if (!$field && in_array($this->getRouteKeyName(), $this->uniqueIds()) && !Str::isUlid($value)) {
            throw (new ModelNotFoundException)->setModel(get_class($this), $value);
        }

        return parent::resolveRouteBindingQuery($query, $value, $field);
    }

    /**
     * Override route bindings for this model to ulid.
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'ulid';
    }
}
