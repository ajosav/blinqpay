<?php

namespace Ajosav\Blinqpay\Router;

use Ajosav\Blinqpay\Exceptions\ConfigurationNotPublishedException;
use Ajosav\Blinqpay\Exceptions\PaymentProcessorException;
use Ajosav\Blinqpay\Facades\Payment;
use Ajosav\Blinqpay\Models\PaymentProcessor;
use Ajosav\Blinqpay\Models\PaymentTransactionLogger;
use Ajosav\Blinqpay\Services\ConfigValidatorService;
use Ajosav\Blinqpay\Services\PaymentProcessorLookup;

class PaymentRouter
{
    public function __construct()
    {
        $this->checkIfConfigFileIsPublished();
        $this->validateRoutingRules();
    }

    public function initiatePayment(float $amount, ?string $currency = 'NGN', ?callable $callback = null): PaymentTransactionLogger
    {
        $processor = $this->getSuitableProcessor($amount, $currency);
        //  Process payment using the Payment Bridge
        return Payment::gateway($processor->slug)->processPayment($amount, $currency, $callback);
    }

    public function getSuitableProcessor(float $amount, string $currency): PaymentProcessor
    {
        $processor = (new PaymentProcessorLookup($amount, $currency))->findSuitablePaymentProcessor();
        throw_if(!$processor, new PaymentProcessorException('No suitable payment processor.'));
        return $processor;
    }

    protected function checkIfConfigFileIsPublished()
    {
        throw_if(!config('blinqpay'), exception: new ConfigurationNotPublishedException('Configuration not published, please publish by running \'php artisan vendor:publish --tag=blinqpay-config\''));
    }

    protected function validateRoutingRules()
    {
        new ConfigValidatorService(config('blinqpay'));
    }
}