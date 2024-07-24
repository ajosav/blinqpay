<?php

namespace Ajosav\Blinqpay\Router;

use Ajosav\Blinqpay\Exceptions\ConfigurationNotPublishedException;
use Ajosav\Blinqpay\Exceptions\PaymentProcessorException;
use Ajosav\Blinqpay\Models\PaymentProcessor;
use Ajosav\Blinqpay\Services\ConfigValidatorService;
use Ajosav\Blinqpay\Services\PaymentProcessorLookup;

class PaymentRouter
{
    public function __construct()
    {
        $this->checkIfConfigFileIsPublished();
        $this->validateRoutingRules();
    }

    public function initiatePayment(float $amount, string $currency = 'NGN')
    {
        $processor = $this->getSuitableProcessor($amount, $currency);
        //  Process payment using the Payment Bridge
    }

    private function getSuitableProcessor(float $amount, string $currency): PaymentProcessor
    {
        $processor = (new PaymentProcessorLookup($amount, $currency))->findSuitablePaymentProcessor();
        throw_if($processor, new PaymentProcessorException('No suitable payment processor.'));
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