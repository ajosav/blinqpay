<?php

namespace Ajosav\Blinqpay\Contracts;

interface PaymentProcessorInterface
{
    public function process(float $amount, string $currency);
}