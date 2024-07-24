<?php

namespace Ajosav\Blinqpay;

use Ajosav\Blinqpay\Router\PaymentRouter;

class Blinqpay
{
    public function __construct(public PaymentRouter $paymentRouter)
    {

    }

    public function initiatePayment()
    {
    }
}