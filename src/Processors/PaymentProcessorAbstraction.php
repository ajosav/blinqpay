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
    protected PaymentProcessorInterface $payment_processor;

    /**
     * @param PaymentProcessorInterface $payment_processor
     */
    public function __construct(PaymentProcessorInterface $payment_processor)
    {
    }

    public static function register(): string
    {
        return static::class;
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
     * @param int $amount
     * @param string|null $currency
     * @return mixed
     */
    public abstract function process(int $amount, ?string $currency = 'NGN');

}