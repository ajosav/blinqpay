<?php

namespace Ajosav\Blinqpay;

use Ajosav\Blinqpay\Models\BlinqpayCurrency;
use Ajosav\Blinqpay\Router\PaymentRouter;
use Symfony\Component\HttpFoundation\Response;

class PaymentProvider
{
    protected float $amount;
    protected string $currency = 'NGN';
    public function __construct(public PaymentRouter $router)
    {}

    public function setCurrency(string $code): self
    {
        $currency = BlinqpayCurrency::firstWhere('code', $code);
        throw_if(!$currency, new \Exception('Invalid currency code provided!', Response::HTTP_INTERNAL_SERVER_ERROR));
        $this->currency = $currency->code;
        return $this;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function pay()
    {

    }
}