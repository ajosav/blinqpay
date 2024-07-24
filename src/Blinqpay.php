<?php

namespace Ajosav\Blinqpay;

use Ajosav\Blinqpay\Router\PaymentRouter;

class Blinqpay
{
    public function __construct(public readonly PaymentRouter $paymentRouter)
    {

    }

    public function initiatePayment(int $amount, string $currency = 'NGN'): ?string
    {
        $payment_transaction = $this->paymentRouter->initiatePayment($amount, $currency);
        return null;
    }
}