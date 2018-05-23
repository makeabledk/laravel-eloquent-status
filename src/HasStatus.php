<?php

namespace Makeable\EloquentStatus;

use ArrayAccess;
use Illuminate\Database\Query\Builder;
use Makeable\QueryKit\QueryKit;

trait HasStatus
{
    use QueryKit;

    /**
     * @param Builder $query
     * @param Status|string $status
     * @return Builder
     */
    public function scopeStatus($query, $status)
    {
        $status = StatusManager::resolveOrFail($this, $status);

        return $status->scope($query);
    }

    /**
     * @param Builder $query
     * @param ArrayAccess|array $statuses
     * @return Builder
     */
    public function scopeStatusIn($query, $statuses)
    {
        return $query->where(function ($query) use ($statuses) {
            foreach ($statuses as $status) {
                $query->orWhere(function ($query) use ($status) {
                    $this->scopeStatus($query, $status);
                });
            }
        });
    }

    /**
     * @param Status|string $status
     * @return bool
     */
    public function checkStatus($status)
    {
        return $this->passesScope('status', $status);
    }

    /**
     * @param ArrayAccess|array $statuses
     * @return bool
     */
    public function checkStatusIn($statuses)
    {
        return $this->passesScope('statusIn', $statuses);
    }
}
