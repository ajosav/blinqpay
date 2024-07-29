<?php

namespace Ajosav\Blinqpay\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransactionLogger extends model
{

    protected $guarded = ['id'];

    public static function booted(): void
    {
        static::creating(function (PaymentTransactionLogger $transaction_logger) {
            $transaction_logger->reference ??= self::generateReference();
        });
    }

    public static function generateReference(): string
    {
        $reference = 'BLINQ_' . mt_rand(100000000, 99999999999) . time();

        while (self::where('reference', $reference)->exists()) {
            self::generateReference();
        }

        return $reference;
    }
}