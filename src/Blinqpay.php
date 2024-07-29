<?php

namespace Ajosav\Blinqpay;

use Ajosav\Blinqpay\Repositories\PaymentProcessorRepository;
use Ajosav\Blinqpay\Router\PaymentRouter;

class Blinqpay
{
    protected $processors = [];

    public function __construct(
        public readonly PaymentRouter $paymentRouter,
        public readonly PaymentProcessorRepository $paymentProcessorRepository
    )
    {

    }

    public function initiatePayment(?float $amount = null, ?string $currency = null, ?callable $callback = null): PaymentProvider
    {
        $payment_provider = new PaymentProvider($this->paymentRouter);
        if ($amount) {
            $payment_provider->setAmount($amount);
        }

        if ($currency) {
            $payment_provider->setCurrency($currency);
        }
        return $payment_provider;
    }

    public function processor(): Processor
    {
        return new Processor($this->paymentProcessorRepository);
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