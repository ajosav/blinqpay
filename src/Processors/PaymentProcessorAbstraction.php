<?php

namespace Ajosav\Blinqpay\Processors;

use Ajosav\Blinqpay\Contracts\PaymentProcessorInterface;

/**
 *
 */
abstract class PaymentProcessorAbstraction
{
    /**
     * @var PaymentProcessorInterface
     */
    public PaymentProcessorInterface $payment_processor;

    /**
     * @param PaymentProcessorInterface $payment_processor
     */
    public function __construct(PaymentProcessorInterface $payment_processor)
    {
    }

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
     * @return mixed
     */
    public abstract function process(float $amount, ?string $currency = 'NGN');

}