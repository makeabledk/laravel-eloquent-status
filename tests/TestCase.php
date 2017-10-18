<?php

namespace Makeable\EloquentStatus\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Makeable\QueryKit\QueryKitServiceProvider;

class TestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        putenv('APP_ENV=testing');
        putenv('DB_CONNECTION=sqlite');
        putenv('DB_DATABASE=:memory:');

        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        $app->register(QueryKitServiceProvider::class);

        return $app;
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('status')->nullable();
        });
    }
}
