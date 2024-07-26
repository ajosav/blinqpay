<?php

namespace Ajosav\Blinqpay\Repositories;

use Ajosav\Blinqpay\DTO\PaymentProcessorDto;
use Ajosav\Blinqpay\Models\PaymentProcessor;
use Ajosav\Blinqpay\Services\PaymentProcessorManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 *
 */
class PaymentProcessorRepository
{
    /**
     * @param PaymentProcessor $paymentProcessorModel
     * @param PaymentProcessorManager $paymentProcessorManager
     */
    public function __construct(
        public readonly PaymentProcessor $paymentProcessorModel,
        public readonly PaymentProcessorManager $paymentProcessorManager
    )
    {
    }

    /**
     * @param PaymentProcessorDto $paymentProcessorDto
     * @return PaymentProcessor
     */
    public function create(PaymentProcessorDto $paymentProcessorDto): PaymentProcessor
    {
        return DB::transaction(function() use ($paymentProcessorDto) {
            $slug = Str::slug(Str::trim(Str::squish($paymentProcessorDto->name)));
            return $this->createOrUpdateProcessor($slug, $paymentProcessorDto);
        });
    }

    /**
     * @param string $slug
     * @param PaymentProcessorDto $paymentProcessorDto
     * @return PaymentProcessor
     */
    public function update(string $slug, PaymentProcessorDto $paymentProcessorDto): PaymentProcessor
    {
        return tap($this->findOne($slug), function(PaymentProcessor $processor) use ($paymentProcessorDto) {
            return $this->createOrUpdateProcessor($processor->slug, $paymentProcessorDto);
        });
    }

    /**
     * @return Collection
     */
    public function findAll(): Collection
    {
        return $this->paymentProcessorModel->with('settings', 'currencies')->all();
    }

    /**
     * @param string $slug
     * @return PaymentProcessor
     */
    public function findOne(string $slug): PaymentProcessor
    {
        return $this->paymentProcessorModel->where('slug', $slug)->firstOrFail();
    }

    /**
     * @param string $slug
     * @return ?bool
     */
    public function delete(string $slug): ?bool
    {
        $processor = $this->findOne($slug);
        return DB::transaction(function () use ($processor) {
            $processor->currencies()->delete();
            $processor->settings()->delete();

            // Delete processor class if it exists
            $processor_class_name = $this->paymentProcessorManager->getFileNameFromSlug($processor->slug);
            $this->paymentProcessorManager->delete($processor_class_name);
            return $processor->delete();
        });
    }

    /**
     * @param $slug
     * @param PaymentProcessorDto $paymentProcessorDto
     * @return PaymentProcessor
     */
    private function createOrUpdateProcessor($slug, PaymentProcessorDto $paymentProcessorDto): PaymentProcessor
    {
        $processor = tap($this->paymentProcessorModel::updateOrCreate(
            [
                'name' => $paymentProcessorDto->name
            ],
            [
                'slug' => $slug,
                'status' => $paymentProcessorDto
            ]
        ), function (PaymentProcessor $processor) use ($paymentProcessorDto) {
            $processor->currencies()->sync($paymentProcessorDto->currency_ids);

            $processor_settings = $processor->settings;
            $processor_settings->fees_percentage = $paymentProcessorDto->fees_percentage ?? $processor_settings->fees_percentage;
            $processor_settings->fees_cap = $paymentProcessorDto->fees_cap ?? $processor_settings->fees_cap;
            $processor_settings->reliability = $paymentProcessorDto->reliability ?? 1;
            $processor->save();

            $processor->loadMissing(['currencies', 'settings']);
        });

        $processor_name = Str::studly(str_replace('-', '_', $processor->slug));
        if (empty($this->paymentProcessorManager->getProcessorName($processor_name))) {
            $this->paymentProcessorManager->generate($processor_name);
        }
        $processor->handler_class = str_replace('.php', '::class', $this->paymentProcessorManager->getProcessorName($processor_name));
        return $processor;
    }


}