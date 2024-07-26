<?php

namespace Ajosav\Blinqpay;

use Ajosav\Blinqpay\Models\BlinqpayCurrency;
use Ajosav\Blinqpay\Router\PaymentRouter;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class PaymentProvider
{
    private float $amount;
    private string $currency = 'NGN';
    /**
     * @var callable
     */
    private $callback;

    public function __construct(public PaymentRouter $router)
    {}

    public function setCurrency(string $code): self
    {
        $currency = BlinqpayCurrency::firstWhere('code', $code);
        throw_if(!$currency, new Exception('Invalid currency code provided!', Response::HTTP_INTERNAL_SERVER_ERROR));
        $this->currency = $currency->code;
        return $this;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function setCallBack(callable $callback): self
    {
        $this->callback = $callback;
        return $this;
    }

    public function pay(?callable $callback = null): PaymentTransactionLogger
    {
        if ($callback) {
            $this->callback = $callback;
        }
        return $this->router->initiatePayment($this->amount, $this->currency, $this->callback);
    }
}