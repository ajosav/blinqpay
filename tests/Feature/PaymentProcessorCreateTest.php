<?php

namespace Ajosav\Blinqpay\Tests\Feature;

use Ajosav\Blinqpay\DTO\PaymentProcessorDto;
use Ajosav\Blinqpay\Facades\Blinqpay;
use Ajosav\Blinqpay\Models\BlinqpayCurrency;
use Ajosav\Blinqpay\Models\PaymentProcessor;
use Ajosav\Blinqpay\Models\PaymentProcessorSetting;
use Ajosav\Blinqpay\Services\PaymentProcessorManager;
use Ajosav\Blinqpay\Tests\TestCase;
use Ajosav\Blinqpay\Utils\FilePathUtil;

class PaymentProcessorCreateTest extends TestCase
{
    protected $test_data;

    protected $processor_manager;

    protected $processor_namespace;

    public function setUp(): void
    {
        parent::setUp();

        $this->processor_manager = new PaymentProcessorManager();
        $this->processor_namespace = config('blinqpay.processor_namespace', 'App\\Blinqpay\\Processors');

        $processor = PaymentProcessor::factory()
            ->active()
            ->withSetting()
            ->make();
        $settings = PaymentProcessorSetting::factory()
            ->make();

        $data = PaymentProcessorDto::fromArray([
            'name' => $processor->name,
            'status' => $processor->status,
            'fees_percentage' => $settings->fees_percentage,
            'fees_cap' => $settings->fees_cap,
            'reliability' => $settings->reliability,
            'currency_ids' => BlinqpayCurrency::whereIn('code', ['NGN', 'GBP', 'USD'])->pluck('id')->toArray()
        ]);
        $this->test_data = $data;
    }

    public function test_create_new_processor_method()
    {
        $data = $this->test_data;
        $processor = Blinqpay::processor()->create($data);

        $this->assertDatabaseHas('payment_processors', [
            'name' => $data->name,
            'status' => $data->status
        ]);

        $this->assertEquals($processor->name, $data->name);
    }

    public function test_processor_class_is_created_when_new_processor_is_created()
    {
        $processor = Blinqpay::processor()->create($this->test_data);
        $processor_title = $this->processor_manager->getFileNameFromSlug($processor->slug);
        $file_path = FilePathUtil::pathFromNamespace($this->processor_namespace, $processor_title);

        $this->assertFileExists($file_path);
    }
}