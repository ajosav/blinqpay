<?php

namespace Ajosav\Blinqpay;

use Ajosav\Blinqpay\DTO\PaymentProcessorDto;
use Ajosav\Blinqpay\Models\PaymentProcessor;
use Ajosav\Blinqpay\Repositories\PaymentProcessorRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 *
 */
class Processor
{
    /**
     * @param PaymentProcessorRepository $paymentProcessorRepository
     */
    public function __construct(public PaymentProcessorRepository $paymentProcessorRepository)
    {
    }

    /**
     * @param PaymentProcessorDto $paymentProcessorDto
     * @return PaymentProcessor
     */
    public function create(PaymentProcessorDto $paymentProcessorDto): PaymentProcessor
    {
        return $this->paymentProcessorRepository->create($paymentProcessorDto);
    }

    /**
     * @param string $slug
     * @param PaymentProcessorDto $paymentProcessorDto
     * @return PaymentProcessor
     */
    public function update(string $slug, PaymentProcessorDto $paymentProcessorDto): PaymentProcessor
    {
        return $this->paymentProcessorRepository->update($slug, $paymentProcessorDto);
    }

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->paymentProcessorRepository->findAll();
    }

    /**
     * @param string $slug
     * @return PaymentProcessor
     */
    public function find(string $slug): PaymentProcessor
    {
        return $this->paymentProcessorRepository->findOne($slug);
    }

    /**
     * @param string $slug
     * @return bool
     */
    public function delete(string $slug): bool
    {
        return $this->paymentProcessorRepository->delete($slug);
    }

}