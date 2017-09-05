<?php

namespace Makeable\EloquentStatus;

use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

abstract class Status
{
    /**
     * @var string
     */
    protected $value;

    /**
     * @param $value
     * @param bool $validate
     * @throws InvalidStatusException
     */
    public function __construct($value, bool $validate = true)
    {
        if ($validate && ! static::validate($value)) {
            throw new InvalidStatusException("Invalid status {$value}");
        }
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }

    /**
     * @return Collection
     */
    public static function all()
    {
        return collect(get_class_methods(static::class))
            ->reject(function ($method) {
                return in_array($method, get_class_methods(self::class));
            })
            ->map(function ($status) {
                return new static($status, false);
            });
    }

    /**
     * @param $model
     * @return Status
     * @throws Exception
     */
    public static function guess($model)
    {
        if (! method_exists($model, 'checkStatus')) {
            throw new Exception(class_basename($model).' must implement a checkStatus() method');
        }

        return static::all()->first(function ($status) use ($model) {
            return $model->checkStatus($status);
        });
    }

    /**
     * @param $value
     * @return bool
     */
    public static function validate($value)
    {
        return static::all()->contains($value);
    }

    /**
     * @return string
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * @param $query
     * @return Builder
     */
    public function scope($query)
    {
        return $this->{$this->get()}($query);
    }
}
