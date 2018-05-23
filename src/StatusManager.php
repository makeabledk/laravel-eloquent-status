<?php

namespace Makeable\EloquentStatus;

use ArrayAccess;

class StatusManager
{
    /**
     * @var array
     */
    protected static $map = [];

    /**
     * @param $model
     * @param $status
     */
    public static function bind($model, $status)
    {
        static::$map[$model] = $status;
    }

    /**
     * @param ArrayAccess|array $modelStatusMap
     * @param bool $merge
     * @return array
     */
    public static function map($modelStatusMap, $merge = true)
    {
        if (! $merge) {
            static::$map = [];
        }

        foreach ($modelStatusMap as $model => $status) {
            static::bind($model, $status);
        }

        return static::$map;
    }

    /**
     * @param $model
     * @return Status
     * @throws InvalidStatusException
     */
    public static function resolveOrFail($model, $status)
    {
        if ($status instanceof Status) {
            return $status;
        }

        if ($match = array_get(static::$map, get_class($model))) {
            return new $match($status);
        }

        throw new InvalidStatusException('Couldnt resolve '.$status.' for model '.get_class($model));
    }
}
