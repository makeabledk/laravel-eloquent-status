<?php

namespace Makeable\EloquentStatus\Tests\Feature;

use Makeable\EloquentStatus\InvalidStatusException;
use Makeable\EloquentStatus\Tests\Stubs\OrderStatus;
use Makeable\EloquentStatus\Tests\TestCase;

class BaseStatusTest extends TestCase
{
    public function test_it_detects_statuses()
    {
        $statuses = OrderStatus::all();
        $this->assertEquals(2, $statuses->count());
        $this->assertTrue($statuses->first() instanceof OrderStatus);
    }

    public function test_it_validates_statuses()
    {
        $this->assertTrue(OrderStatus::validate('accepted'));
        $this->assertFalse(OrderStatus::validate('test'));
    }

    public function test_it_instantiates_with_valid_value()
    {
        $status = new OrderStatus('accepted');
        $this->assertTrue($status instanceof OrderStatus);
        $this->assertEquals('accepted', $status->get());
    }

    public function test_it_throws_validation_exception()
    {
        $this->expectException(InvalidStatusException::class);
        new OrderStatus('invalid status');
    }

    public function test_it_casts_to_string()
    {
        $this->assertEquals('accepted', (string) new OrderStatus('accepted'));
    }
}
