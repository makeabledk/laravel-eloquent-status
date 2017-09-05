<?php

namespace Makeable\EloquentStatus\Tests\Stubs;

use Illuminate\Database\Query\Builder;
use Makeable\EloquentStatus\Status;

class OrderStatus extends Status
{
    /**
     * @param Builder $query
     * @return Builder
     */
    public function accepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function declined($query)
    {
        return $query->where('status', 'declined');
    }
}
