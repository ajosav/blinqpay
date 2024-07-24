<?php

namespace Ajosav\Blinqpay\Processors;

use Ajosav\Blinqpay\Contracts\PaymentProcessorInterface;

abstract class BasePaymentProcessor implements PaymentProcessorInterface
{
    public static function register(): string
    {
        return static::class;
    }

    public abstract function process(float $amount, ?string $currency = 'NGN');
}