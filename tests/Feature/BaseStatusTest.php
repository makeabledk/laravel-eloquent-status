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
        $this->assertEquals(3, $statuses->count());
        $this->assertTrue($statuses->first() instanceof OrderStatus);
    }

    public function test_it_skips_static_methods()
    {
        $this->expectException(InvalidStatusException::class);
        new OrderStatus('static method to skip');
    }

    public function test_it_skips_private_methods()
    {
        $this->expectException(InvalidStatusException::class);
        new OrderStatus('private method to skip');
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

    public function test_it_finds_a_valid_status_or_returns_null()
    {
        $this->assertInstanceOf(OrderStatus::class, OrderStatus::find('accepted'));
        $this->assertNull(OrderStatus::find('foobar'));
    }

    public function test_it_casts_to_string()
    {
        $this->assertEquals('accepted', (string) new OrderStatus('accepted'));
    }

    public function test_it_casts_to_array()
    {
        $this->assertEquals('accepted', (new OrderStatus('accepted'))->toArray());
    }

    public function test_it_converts_to_snake_case()
    {
        $this->assertEquals('pending_accept', (string) new OrderStatus('pendingAccept'));
        $this->assertEquals('pending_accept', (string) new OrderStatus('pending_accept'));
    }

    public function test_it_converts_to_title()
    {
        $this->assertEquals('Pending Accept', (new OrderStatus('pendingAccept'))->getTitle());
    }
}
