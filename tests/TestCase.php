<?php

namespace Ajosav\Blinqpay\Tests;

use Ajosav\Blinqpay\BlinqpayServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        restore_error_handler();
        restore_exception_handler();
    }

    /**
     *
     * @param Application $app
     *
     * @return string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            BlinqpayServiceProvider::class
        ];
    }

    /**
     * Define environment setup.
     *
     * @param Application $app
     * @return void
     * @api
     *
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testdb');
        $app['config']->set('database.connections.testdb', [
            'driver' => 'sqlite',
            'database' => ':memory:'
        ]);
    }
}
