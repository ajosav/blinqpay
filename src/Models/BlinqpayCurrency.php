<?php

namespace Ajosav\Blinqpay\Models;

use Illuminate\Database\Eloquent\Model;

class BlinqpayCurrency extends Model
{
    protected $table = 'blinqpay_currencies';
    protected $fillable = ['id', 'name', 'code'];
}
