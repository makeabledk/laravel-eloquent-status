<?php

namespace Makeable\EloquentStatus\Tests\Feature;

use Makeable\EloquentStatus\InvalidStatusException;
use Makeable\EloquentStatus\StatusManager;
use Makeable\EloquentStatus\Tests\Stubs\Order;
use Makeable\EloquentStatus\Tests\Stubs\OrderStatus;
use Makeable\EloquentStatus\Tests\TestCase;

class QueryStatusTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        StatusManager::map([], false);
    }

    public function test_it_queries_database()
    {
        Order::create(['status' => 1]);
        Order::create(['status' => 1]);
        Order::create(['status' => 0]);

        $this->assertEquals(2, Order::status(new OrderStatus('accepted'))->count());
        $this->assertEquals(1, Order::status(new OrderStatus('declined'))->count());
    }

    public function test_it_checks_a_model_against_a_status()
    {
        $order = Order::create(['status' => 1]);

        $this->assertTrue($order->checkStatus(new OrderStatus('accepted')));
        $this->assertFalse($order->checkStatus(new OrderStatus('declined')));
    }

    public function test_it_guesses_a_models_status()
    {
        $model = Order::create(['status' => 0]);
        $this->assertEquals('declined', OrderStatus::guess($model)->get());

        $model = Order::create(['status' => null]);
        $this->assertEquals('pending_accept', OrderStatus::guess($model)->get());
    }

    /** @test **/
    public function it_accepts_valid_string_status_when_mapped_in_manager()
    {
        $model = Order::create(['status' => 1]);

        $this->expectException(InvalidStatusException::class);
        $model->checkStatus('accepted');

        StatusManager::bind(Order::class, OrderStatus::class);
        $this->assertTrue($model->checkStatus('accepted'));
    }

    /** @test **/
    public function it_queries_database_where_status_in_haystack()
    {
        Order::create(['status' => 1]);
        Order::create(['status' => 0]);
        Order::create(['status' => null]);

        StatusManager::bind(Order::class, OrderStatus::class);

        $acceptedOrDeclined = Order::statusIn(['accepted', 'declined'])->get();

        $this->assertCount(2, $acceptedOrDeclined);
        $this->assertEquals([1, 2], $acceptedOrDeclined->pluck('id')->toArray());
    }

    /** @test **/
    public function it_checks_models_if_status_in_haystack()
    {
        StatusManager::bind(Order::class, OrderStatus::class);

        $this->assertTrue(Order::create(['status' => 1])->checkStatusIn(['accepted', 'declined']));
        $this->assertTrue(Order::create(['status' => 0])->checkStatusIn(['accepted', 'declined']));
        $this->assertFalse(Order::create(['status' => null])->checkStatusIn(['accepted', 'declined']));
    }
}
