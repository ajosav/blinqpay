<?php

namespace Ajosav\Blinqpay\DTO;

class PaymentProcessorDto
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $status,
        public readonly array $currency_ids,
        public readonly float $fees_percentage,
        public readonly ?float $fees_cap,
        public readonly ?int $reliability
    )
    {
    }

    public static function fromArray(array $data)
    {
        return new self(
            name: $data['name'],
            status: $data['status'] ?? 'active',
            currency_ids: $data['currency_ids'],
            fees_percentage: $data['fees_percentage'],
            fees_cap: $data['fees_cap'] ?? null,
            reliability: $data['reliability'] ?? 1
        );
    }
}