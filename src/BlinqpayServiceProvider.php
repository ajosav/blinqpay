<?php

namespace Ajosav\Blinqpay;

use Ajosav\Blinqpay\Commands\PaymentProcessorCommand;
use Ajosav\Blinqpay\Processors\BasePaymentProcessor;
use Ajosav\Blinqpay\Processors\PaymentProcessor;
use Ajosav\Blinqpay\Repositories\PaymentProcessorRepository;
use Ajosav\Blinqpay\Router\PaymentRouter;
use Ajosav\Blinqpay\Services\PaymentProcessorManager;
use Ajosav\Blinqpay\Utils\FilePathUtil;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Ajosav\Blinqpay\Facades\Blinqpay;
use Ajosav\Blinqpay\Blinqpay as BaseBlinqpay;

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

        $this->registerFacades();
        $this->registerRoutes();
        $this->registerViews();
        $this->registerMigrations();
        $this->registerProccessors();
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        // register the package config
        $this->mergeConfigFrom(__DIR__ . '/../config/blinqpay.php', 'blinqpay');
    }

    /**
     * Registers the package custom commands with the laravel command
     * @return void
     */
    protected function registerCommands()
    {
        $this->commands([
            PaymentProcessorCommand::class
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
            $repository = app(PaymentProcessorRepository::class);
            return new BaseBlinqpay(new PaymentRouter, $repository);
        });

        $this->app->singleton('PaymentProcessorAdapter', function ($app) {
            return new PaymentProcessor(new PaymentProcessorManager);
        });
    }

    public function registerProccessors()
    {
        $processors = [];
        $namespace = config('blinqpay.processor_namespace', 'App\\Cliqpay\\Processors');
        $path = FilePathUtil::getAppPathFromNamespace($namespace);

        if (File::isDirectory($path)) {
            $files = File::files($path);
            $paymentProcessorManager = app(PaymentProcessorManager::class);

            foreach ($files as $file) {
                $file_name = str_replace('.php', '', $file->getBasename());
                $className = $paymentProcessorManager->getClassPath($file_name);
                if (class_exists($className) && is_subclass_of($className, BasePaymentProcessor::class)) {
                    $processors[] = $className::register();
                }
            }
        }
        Blinqpay::setProcessors($processors);
    }
}
