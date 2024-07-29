<?php

namespace Ajosav\Blinqpay\Tests\Unit\Facades;

use Ajosav\Blinqpay\Facades\Blinqpay as BlinqpayFacade;
use Ajosav\Blinqpay\PaymentProvider;
use Ajosav\Blinqpay\Processor;
use Ajosav\Blinqpay\Tests\TestCase;

class BlinqpayFacadeTest extends TestCase
{
    public function test_processor_is_injected_when_using_processor_method_on_facade()
    {
        $this->assertInstanceOf(Processor::class, BlinqPayFacade::processor());
    }

    public function test_payment_provider_is_injected_when_payment_is_initiated_through_facade()
    {
        $this->assertInstanceOf(PaymentProvider::class, BlinqPayFacade::initiatePayment());
    }
}
