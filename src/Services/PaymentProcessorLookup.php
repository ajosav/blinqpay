<?php

namespace Ajosav\Blinqpay\Services;

use Ajosav\Blinqpay\Exceptions\PaymentProcessorException;
use Ajosav\Blinqpay\Models\PaymentProcessor;
use Illuminate\Contracts\Database\Eloquent\Builder;

/**
 *
 */
class PaymentProcessorLookup
{
    /**
     * @var
     */
    protected $payment_processors;

    /**
     * @param int $amount
     * @param string|null $currency
     */
    public function __construct(public int $amount, public ?string $currency)
    {
        $this->getPaymentProcessors()
            ->computePaymentProcessorScores();
    }

    /**
     * @return PaymentProcessor|null
     */
    public function findSuitablePaymentProcessor(): ?PaymentProcessor
    {
        return $this->payment_processors->sortBy([['score', 'desc']])->first();
    }

    /**
     * @return $this
     */
    private function getPaymentProcessors(): self
    {
        $this->payment_processors = PaymentProcessor::with('settings')
                                        ->has('settings')
                                        ->whereStatus('active')
                                        ->whereHas('currencies',
                                            fn (Builder $currency) =>
                                            $currency->whereIn('code', $this->currency)
                                        )->get();
        return $this;
    }

    /**
     * @return $this
     */
    private function computePaymentProcessorScores():self
    {
        if (!$this->payment_processors) {
            $this->getPaymentProcessors();
        }

        throw_if($this->payment_processors->isEmpty(), new PaymentProcessorException('No suitable payment processor.'));

        $this->payment_processors = $this->payment_processors->map->computeScore($this->amount);
        return $this;
    }

}