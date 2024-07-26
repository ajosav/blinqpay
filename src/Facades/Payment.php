<?php

namespace Ajosav\Blinqpay\Facades;

use Illuminate\Support\Facades\Facade;

class Payment extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'PaymentProcessorAdapter';
    }
}