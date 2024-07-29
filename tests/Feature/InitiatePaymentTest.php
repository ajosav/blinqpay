<?php

namespace Ajosav\Blinqpay\Tests\Feature;

use Ajosav\Blinqpay\DTO\PaymentProcessorDto;
use Ajosav\Blinqpay\Facades\Blinqpay;
use Ajosav\Blinqpay\Facades\Payment;
use Ajosav\Blinqpay\Models\BlinqpayCurrency;
use Ajosav\Blinqpay\Models\PaymentProcessor;
use Ajosav\Blinqpay\Models\PaymentProcessorSetting;
use Ajosav\Blinqpay\Models\PaymentTransactionLogger;
use Ajosav\Blinqpay\Tests\TestCase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;

class InitiatePaymentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->seedProcessor();

    }

    public function seedProcessor()
    {
        $data = $this->happyPaymentDataProvider();
        foreach ($data as $test_data) {
            $name = Str::title(str_replace('-', ' ', $test_data[2]));
            $processor = PaymentProcessor::factory()
                ->active()
                ->withSetting()
                ->make([
                    'name' => $name
                ]);
            $settings = PaymentProcessorSetting::factory()
                ->make();

            $data = PaymentProcessorDto::fromArray([
                'name' => $processor->name,
                'status' => $processor->status,
                'fees_percentage' => $settings->fees_percentage,
                'fees_cap' => $settings->fees_cap,
                'reliability' => $settings->reliability,
                'currency_ids' => BlinqpayCurrency::where('code', $test_data[1])->pluck('id')->toArray()
            ]);
            Blinqpay::processor()->create($data);
        }
    }

    public static function happyPaymentDataProvider(): array
    {
        return [
            [4000, 'NGN', 'ngn-processor'],
            [150, 'USD', 'usd-processor'],
            [70000, 'NGN', 'ngn-processor'],
            [3200, 'NGN', 'ngn-processor'],
            [4.56, 'USD', 'usd-processor'],
            [600, 'GHC', 'ghc-processor'],
            [6000, 'CAD', 'cad-processor'],
            [70000, 'GHC', 'ghc-processor'],
            [1000000, 'NGN', 'ngn-processor'],
            [2200, 'CAD', 'cad-processor']
        ];
    }

    #[DataProvider('happyPaymentDataProvider')]
    public function test_user_can_make_payment_using_the_payment_provider_facade($amount, $currency_code, $expected_processor_slug)
    {
        $processor = PaymentProcessor::where('slug', $expected_processor_slug)->first();
        $logger = PaymentTransactionLogger::create([
            'reference' => Str::random(),
            'payment_processor_id' => $processor->id,
            'amount' => ($amount * 100),
            'blinqpay_currency_id' => $processor->currencies()->where('code', $currency_code)->first()
        ]);
        Payment::shouldReceive('gateway')
            ->with($expected_processor_slug)
            ->once()
            ->andReturnSelf();
        Payment::shouldReceive('processPayment')
            ->once()
            ->andReturn($logger);
        $make_payment = Blinqpay::initiatePayment()
            ->setAmount($amount)
            ->setCurrency($currency_code)
            ->pay();

        $this->assertEquals($logger->reference, $make_payment->reference);

    }
}