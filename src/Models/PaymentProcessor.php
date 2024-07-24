<?php

namespace Ajosav\Blinqpay\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PaymentProcessor extends Model
{
    protected $guarded = ['id'];

    public function currencies(): HasMany
    {
        return $this->hasMany(BlinqpayCurrency::class);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(PaymentProcessorSetting::class);
    }

    public function score(): Attribute
    {
        return Attribute::make(
            get: function(string $value) {
                $settings = $this->settings;
                if (!$settings) {
                    return 0;
                }

                $scoring_rules = config('blinqpay.routing_rules');
                foreach ()
                return [];
            }
        );
    }
}