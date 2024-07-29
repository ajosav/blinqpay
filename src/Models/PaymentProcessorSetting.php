<?php

namespace Ajosav\Blinqpay\Models;

use Database\Factories\PaymentProcessorSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentProcessorSetting extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    /**
     * Create a new factory instance for the model.
     *
     * @return PaymentProcessorSettingFactory
     */
    protected static function newFactory(): PaymentProcessorSettingFactory
    {
        return PaymentProcessorSettingFactory::new();
    }
}