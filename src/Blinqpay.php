<?php

namespace Ajosav\Blinqpay;

use Ajosav\Blinqpay\Router\PaymentRouter;

class Blinqpay
{
    protected $processors = [];

    public function __construct(public readonly PaymentRouter $paymentRouter)
    {

    }

    public function initiatePayment(float $amount, string $currency = 'NGN'): ?string
    {
        $payment_transaction = $this->paymentRouter->initiatePayment($amount, $currency);
        return null;
    }

    public function setProcessors(array $processors)
    {
        $this->processors = array_merge($processors, $this->processors);
    }

    public function getSupportedProcessors(): array
    {
        return array_reverse($this->processors);
    }
}