<?php

namespace Ajosav\Blinqpay\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentProcessorSetting extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
}