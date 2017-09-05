<?php

namespace Makeable\EloquentStatus;

use Illuminate\Database\Query\Builder;
use Makeable\QueryKit\QueryKit;

trait HasStatus
{
    use QueryKit;

    /**
     * @param Status $status
     * @return Builder
     */
    public function scopeStatus($query, Status $status)
    {
        return $status->scope($query);
    }

    /**
     * @param Status $status
     * @return bool
     */
    public function checkStatus(Status $status)
    {
        return $this->passesScope('status', $status);
    }
}
