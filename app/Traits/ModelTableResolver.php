<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * The purpose of the trait is to get name from model instance and make accesible as a constant.
 */
trait ModelTableResolver
{
    private static $__table = null;
    /**
     * Returns true only if this trait used inside a model.
     * @return bool
     */
    protected static function isModel(): bool
    {
        return is_a(static::class, Model::class, true) or is_subclass_of(static::class, Model::class);
    }

    /**
     * Returns table name from static table name
     * @return string
     */
    public static function table(): string|null
    {
        return static::isModel() ? static::$__table ?? static::getTableFromInstance() : null;
    }

    /**
     * This will be invoked when the static prop $__table is null.
     * It sets the table name to the Model as a static property and returns the table name.
     */
    protected static function getTableFromInstance(): string
    {
        return static::$__table = app(static::class)->getTable();
    }
}
