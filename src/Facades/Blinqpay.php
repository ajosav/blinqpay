<?php

namespace Ajosav\Blinqpay\Facades;

use Illuminate\Support\Facades\Facade;

class Blinqpay extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'Blinqpay';
    }
}