<?php

namespace Ajosav\Blinqpay\Processors;

use Ajosav\Blinqpay\Contracts\PaymentProcessorInterface;

class BlinqpayPaymentProcessor implements PaymentProcessorInterface
{
    public function process(float $amount, ?string $currency = 'NGN')
    {
        return '';
    }
}