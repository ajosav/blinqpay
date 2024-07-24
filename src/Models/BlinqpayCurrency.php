<?php

namespace Ajosav\Blinqpay\Models;

use Illuminate\Database\Eloquent\Model;

class BlinqpayCurrency extends Model
{
    protected $fillable = ['id', 'name', 'code'];
}