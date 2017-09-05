<?php

namespace Makeable\EloquentStatus\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;
use Makeable\EloquentStatus\HasStatus;

class Order extends Model
{
    use HasStatus;

    protected $guarded = [];

    public $timestamps = false;
}