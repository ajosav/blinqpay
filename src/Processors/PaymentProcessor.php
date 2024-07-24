<?php

namespace Ajosav\Blinqpay\Processors;

use Ajosav\Blinqpay\Contracts\PaymentProcessorInterface;
use Ajosav\Blinqpay\Exceptions\PaymentProcessorException;
use Ajosav\Blinqpay\Facades\Blinqpay;
use Illuminate\Support\Str;
use function PHPUnit\Framework\assertInstanceOf;

class PaymentProcessor extends PaymentProcessorAbstraction
{
    protected $payment_gateway;
    protected string $gateway;

    public function gateway(string $gateway)
    {
        $this->payment_gateway = $this->getPaymentGateway($gateway);
        assertInstanceOf(PaymentProcessorInterface::class, $this->payment_gateway);
        $this->setProcessor($this->payment_gateway);
    }

    protected function getPaymentGateway(string $gateway)
    {
        $gateway = Str::studly(str_replace('-', '_', $gateway));
        foreach (Blinqpay::getSupportedProcessors() as $processor) {
            $class = new \ReflectionClass($processor);
            if ($class->getShortName() === $gateway) {
                return $processor;
            }
        }

        throw new PaymentProcessorException('The payment processor provided isn\'t recognized');
    }

    public function process(int $amount, ?string $currency = 'NGN')
    {
        // TODO: Implement process() method.
    }
}