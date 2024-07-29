<?php

namespace Ajosav\Blinqpay\Tests\Unit;

use Ajosav\Blinqpay\Enums\ProcessorStatusEnum;
use Ajosav\Blinqpay\Exceptions\ConfigurationNotPublishedException;
use Ajosav\Blinqpay\Exceptions\PaymentProcessorException;
use Ajosav\Blinqpay\Models\BlinqpayCurrency;
use Ajosav\Blinqpay\Models\PaymentProcessor;
use Ajosav\Blinqpay\Router\PaymentRouter;
use Ajosav\Blinqpay\Tests\TestCase;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;

class PaymentProcessorRouterTest extends TestCase
{
    protected $processors;

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

    public static function failurePaymentDataProvider(): array
    {
        return [
            [4000, 'GBP', 'gbp-processor'],
            [150, 'UAH', 'uah-processor'],
            [70000, 'TVD', 'tvd-processor'],
            [3200, 'TRL', 'trl-processor'],
            [4.56, 'TTD', 'ttd-processor'],
            [600, 'THB', 'thb-processor'],
            [6000, 'ZAR', 'zar-processor'],
            [70000, 'LKR', 'lkr-processor']
        ];
    }

    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_slug_is_generated_when_processor_is_created()
    {
        $processor = PaymentProcessor::factory()->create([
            'name' => 'Test Payment Processor'
        ]);
        $expected_slug = 'test-payment-processor';

        $this->assertDatabaseHas('payment_processors', [
            'name' => 'Test Payment Processor',
            'slug' => 'test-payment-processor'
        ]);

        $this->assertModelExists($processor);
        $this->assertNotEmpty($processor->slug);
        $this->assertEquals($processor->slug, $expected_slug);
    }

    public function test_router_throws_exception_when_config_file_is_not_published()
    {
        $config = app(ConfigRepository::class);
        $config->set('blinqpay', null);

        $this->expectException(ConfigurationNotPublishedException::class);
        $this->expectExceptionMessage('Configuration not published, please publish by running \'php artisan vendor:publish --tag=blinqpay-config\'');

        new PaymentRouter();
    }

    #[DataProvider('happyPaymentDataProvider')]
    public function test_most_suitable_processor_is_selected_based_on_the_transaction_cost_and_currency($amount, $currency_code, $expected_processor_slug)
    {
        $this->processors = $this->createProcessor();
        $custom_processor = $this->getCustomProcessor($expected_processor_slug, $currency_code, true);
        $router = new PaymentRouter();
        $recommended_processor = $router->getSuitableProcessor($amount, $currency_code);
        $this->assertEquals($recommended_processor->slug, $custom_processor->slug);
    }

    private function createProcessor()
    {
        return PaymentProcessor::factory()
            ->count(20)
            ->sequence(
                ['status' => 'active'],
                ['status' => 'inactive']
            )
            ->withSetting()
            ->create();
    }

    private function getCustomProcessor(string $slug, string $currency_code, ?bool $should_create = false): ?PaymentProcessor
    {
        $processor = PaymentProcessor::where('status', ProcessorStatusEnum::ACTIVE->value)->whereHas('currencies', fn($currency) => $currency->where('code', $currency_code))->first();
        $processor_name = Str::title(str_replace('-', ' ', $slug));

        if (!$processor && $should_create) {
            $processor = PaymentProcessor::factory()
                ->active()
                ->withSetting()
                ->create([
                    'name' => $processor_name
                ]);

            $currency_id = BlinqpayCurrency::where('code', $currency_code)->pluck('id');
            $processor->currencies()->sync($currency_id);
        } else {
            $processor?->update([
                'name' => $processor_name,
                'slug' => $slug
            ]);
        }
        $processor?->settings()->update([
            'fees_percentage' => 0.1,
            'fees_cap' => 10,
            'reliability' => 20
        ]);

        return $processor;
    }

    #[DataProvider('failurePaymentDataProvider')]
    public function test_payment_processor_exception_is_thrown_when_suitable_processor_is_not_found($amount, $currency_code, $expected_processor_slug)
    {
        $this->processors = $this->createProcessor();
        $custom_processor = $this->getCustomProcessor($expected_processor_slug, $currency_code);
        $this->expectException(PaymentProcessorException::class);
        $this->expectExceptionMessage('No suitable payment processor.');
        $router = new PaymentRouter();
        $recommended_processor = $router->getSuitableProcessor($amount, $currency_code);
        $this->assertNotEquals($custom_processor?->slug, $recommended_processor?->slug);
    }
}
