<?php

namespace Ajosav\Blinqpay\Tests;

use Ajosav\Blinqpay\BlinqpayServiceProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__ . './../database/factories');
    }

    /**
     *
     * @param  Application $app
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
     * @api
     *
     * @param Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testdb');
        $app['config']->set('database.connections.testdb', [
            'driver' => 'sqlite',
            'database' => ':memory'
        ]);
    }
}
