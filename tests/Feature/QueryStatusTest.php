<?php

namespace Makeable\EloquentStatus\Tests\Feature;

use Makeable\EloquentStatus\Tests\Stubs\Order;
use Makeable\EloquentStatus\Tests\Stubs\OrderStatus;
use Makeable\EloquentStatus\Tests\TestCase;

class QueryStatusTest extends TestCase
{
    public function test_it_queries_database()
    {
        Order::create(['status' => 'accepted']);
        Order::create(['status' => 'accepted']);
        Order::create(['status' => 'declined']);

        $this->assertEquals(2, Order::status(new OrderStatus('accepted'))->count());
        $this->assertEquals(1, Order::status(new OrderStatus('declined'))->count());
    }

    public function test_it_checks_a_model_against_a_status()
    {
        $order = Order::create(['status' => 'accepted']);

        $this->assertTrue($order->checkStatus(new OrderStatus('accepted')));
        $this->assertFalse($order->checkStatus(new OrderStatus('declined')));
    }

    public function test_it_guesses_a_models_status()
    {
        $model = Order::create(['status' => 'declined']);
        $this->assertEquals('declined', OrderStatus::guess($model)->get());
    }
}
