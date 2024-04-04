<?php

namespace Makeable\EloquentStatus\Tests\Stubs;

use Illuminate\Database\Query\Builder;
use Makeable\EloquentStatus\Status;

class OrderStatus extends Status
{
    /**
     * @param  $query
     * @return mixed
     */
    public function pendingAccept($query)
    {
        return $query->whereNull('status');
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function accepted($query)
    {
        return $query->where('status', 1);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function declined($query)
    {
        return $query->where('status', 0);
    }

    /**
     * @return void
     */
    private function privateMethodToIgnore()
    {
        //
    }

    /**
     * @return void
     */
    public static function staticMethodToIgnore()
    {
        //
    }
}
