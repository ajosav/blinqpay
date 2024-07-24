<?php

namespace Ajosav\Blinqpay;

use Ajosav\Blinqpay\Router\PaymentRouter;
use Illuminate\Support\ServiceProvider;

class BlinqpayServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->publishConfigs();
            $this->registerCommands();
        }

        $this->registerRoutes();
        $this->registerViews();
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        // register the package config
        $this->mergeConfigFrom(__DIR__ . '/../config/blinqpay.php', 'cliqpay');
    }

    /**
     * Registers the package custom commands with the laravel command
     * @return void
     */
    protected function registerCommands()
    {
        $this->commands([

        ]);
    }

    /**
     * Registers the package routes
     * @return void
     */
    protected function registerRoutes()
    {
        $this->loadRoutesFrom(realpath(__DIR__ . '/../routes/api.php'));
        $this->loadRoutesFrom(realpath(__DIR__ . '/../routes/web.php'));
    }

    /**
     * Register the package migrations
     */
    public function registerMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Register the package views
     */
    public function registerViews()
    {
        $this->loadViewsFrom(realpath(__DIR__ . '/../resources/views'), 'cliqpay');
    }

    /**
     * Publish the config file, so it can be customized within our laravel app
     * @return void
     */
    protected function publishConfigs()
    {
        $this->publishes([
            __DIR__ . '/../config/blinqpay.php' => config_path('blinqpay.php'),
        ], 'blinqpay-config');
    }

    public function registerFacades()
    {
        $this->app->singleton('Blinqpay', function ($app) {
            return \Ajosav\Blinqpay\Blinqpay(PaymentRouter::class);
        });
    }

}