<?php

namespace Ajosav\Blinqpay\Tests\Unit\Commands;

use Ajosav\Blinqpay\Services\PaymentProcessorManager;
use Ajosav\Blinqpay\Tests\TestCase;
use Ajosav\Blinqpay\Utils\FilePathUtil;
use Illuminate\Support\Facades\File;

class ProcessorCommandTest extends TestCase
{
    public function test_command_prints_error_message_when_a_process_with_a_given_name_already_exist()
    {
        File::shouldReceive('exists')->once()->andReturn(true);
        $this->artisan('blinqpay:processor', ['name' => 'TestPaymentProcessor'])
            ->expectsOutput('File already exists')
            ->assertExitCode(0);
    }

    public function test_processor_is_created_when_command_is_executed()
    {
        $processor_name = 'TestPaymentProcessor';
        $base_path = config('blinqpay.processor_namespace', 'App\\Blinqpay\\Processors');
        $app_path = FilePathUtil::pathFromNamespace($base_path, $processor_name);
        $processor_manager = new PaymentProcessorManager;
        $stub_content = $processor_manager->getStubContent($processor_name);

        File::shouldReceive('exists')
            ->once()
            ->with($app_path)
            ->andReturn(false);

        File::shouldReceive('isDirectory')
            ->once()
            ->with(dirname($app_path))
            ->andReturn(false);

        File::shouldReceive('makeDirectory')
            ->once()
            ->with(dirname($app_path), 0777, true, true)
            ->andReturn(true);

        File::shouldReceive('put')
            ->once()
            ->with($app_path, $stub_content)
            ->andReturn(true);

        $this->artisan('blinqpay:processor', ['name' => $processor_name])
            ->expectsOutput('TestPaymentProcessor payment processor created successfully')
            ->assertExitCode(0);
    }
}
