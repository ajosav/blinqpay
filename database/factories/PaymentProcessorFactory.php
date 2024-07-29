<?php

namespace Database\Factories;

use Ajosav\Blinqpay\Models\BlinqpayCurrency;
use Ajosav\Blinqpay\Models\PaymentProcessor;
use Ajosav\Blinqpay\Enums\ProcessorStatusEnum;
use Ajosav\Blinqpay\Models\PaymentProcessorSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentProcessorFactory extends Factory
{
    protected $model = PaymentProcessor::class;

    /**
     * @return array|mixed[]
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'status' => ProcessorStatusEnum::values()[rand(0, 1)],
        ];
    }

    /**
     * Indicate that the processor is active.
     *
     * @return Factory
     */
    public function active(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => ProcessorStatusEnum::ACTIVE->value,
            ];
        });
    }

    /**
     * Indicate that the processor is active.
     *
     * @return Factory
     */
    public function inActive(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => ProcessorStatusEnum::INACTIVE->value,
            ];
        });
    }

    /**
     * create a setting for the processor.
     *
     * @return Factory
     */
    public function withSetting(): Factory
    {
        return $this->has(
            PaymentProcessorSetting::factory()->state(function (array $attributes, PaymentProcessor $processor) {
                return ['payment_processor_id' => $processor->id];
            }), 'settings');
    }


    public function configure()
    {
        return $this->afterCreating(function (PaymentProcessor $processor) {
            if ($processor->status ===ProcessorStatusEnum::ACTIVE->value) {
                $currencies = BlinqpayCurrency::whereIn('code', ['USD', 'NGN', 'CAD', 'GHC'])->get();
                $randomized_currency = $currencies->random(rand(1, $currencies->count()));
                $processor->currencies()->sync($randomized_currency->pluck('id'));
            }
        });
    }
}
