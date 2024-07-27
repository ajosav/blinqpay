<?php

namespace Ajosav\Blinqpay\Processors;

use Ajosav\Blinqpay\Contracts\PaymentProcessorInterface;
use Ajosav\Blinqpay\Exceptions\PaymentProcessorException;
use Ajosav\Blinqpay\Facades\Blinqpay;
use Ajosav\Blinqpay\Models\PaymentTransactionLogger;
use Ajosav\Blinqpay\Services\PaymentProcessorManager;
use ReflectionClass;
use function PHPUnit\Framework\assertInstanceOf;
use Ajosav\Blinqpay\Models\PaymentProcessor as PaymentProcessorModel;

class PaymentProcessor extends PaymentProcessorAbstraction
{
    protected $payment_gateway;
    protected $processor;

    public function __construct(public PaymentProcessorManager $paymentProcessorManager)
    {
    }

    public function gateway(string $gateway): self
    {
        if (!$this->processor) {
            $processor = PaymentProcessorModel::whereSlug($gateway)->first();
            throw_if (!$processor, new PaymentProcessorException('There\'s no processor available at the moment'));
            $this->processor = $processor;
        }

        $this->payment_gateway = $this->getPaymentGateway($gateway);

        assertInstanceOf(PaymentProcessorInterface::class, $this->payment_gateway);
        $this->setProcessor($this->payment_gateway);
        return $this;
    }

    protected function getPaymentGateway(string $gateway):PaymentProcessorInterface
    {
        $gateway = $this->paymentProcessorManager->getFileNameFromSlug($gateway);

        if (empty(Blinqpay::getSupportedProcessors())) {
            $processor = $this->paymentProcessorManager->getClassPath($gateway);

            return new $processor;
        }

        foreach (Blinqpay::getSupportedProcessors() as $processor) {
            $class = new ReflectionClass($processor);
            if ($class->getShortName() === $gateway) {
                return new $processor;
            }
        }

        throw new PaymentProcessorException('The payment processor provided isn\'t recognized');
    }

    public function processPayment(float $amount, ?string $currency = 'NGN', ?callable $callback = null): PaymentTransactionLogger
    {
        $this->setProcessorIfNotAlreadySet();
        $this->payment_processor->process($amount, $currency);
        $currency_id = $this->processor->currencies()->where('code', $currency)->first()?->id;
        $transaction_log = PaymentTransactionLogger::create([
            'payment_processor_id' => $this->processor->id,
            'amount' => ($amount * 100), //saving in kobo
            'blinqpay_currency_id' => $currency_id
        ]);
        if ($callback) {
            $callback($transaction_log->reference, $this->processor);
        }
        return $transaction_log;
    }

    private function setProcessorIfNotAlreadySet(): void
    {
        if (!is_null($this->payment_gateway)) {
            return;
        }

        $payment_processor = PaymentProcessorModel::whereHas('currency', fn($currency) => $currency->where('code', $currency))->first();
        throw_if(!$payment_processor, new PaymentProcessorException('There\'s no processor available at the moment'));
        $this->processor = $payment_processor;
        $this->gateway($payment_processor->slug);
    }
}