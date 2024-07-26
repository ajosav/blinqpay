<?php

namespace Ajosav\Blinqpay\Processors;

use Ajosav\Blinqpay\Contracts\PaymentProcessorInterface;
use Ajosav\Blinqpay\PaymentTransactionLogger;

/**
 *
 */
abstract class PaymentProcessorAbstraction
{
    /**
     * @var PaymentProcessorInterface
     */
    protected PaymentProcessorInterface $payment_processor;
    /**
     * @param PaymentProcessorInterface $payment_processor
     * @return void
     */
    public function setProcessor(PaymentProcessorInterface $payment_processor)
    {
        $this->payment_processor = $payment_processor;
    }

    /**
     * @param float $amount
     * @param string|null $currency
     * @param callable|null $callback
     * @return mixed
     */
    public abstract function processPayment(float $amount, ?string $currency = 'NGN', ?callable $callback = null): PaymentTransactionLogger;

}