<?php

namespace Ajosav\Blinqpay\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PaymentProcessor extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * @return HasMany
     */
    public function currencies(): BelongsToMany
    {
        return $this->belongsToMany(BlinqpayCurrency::class, 'payment_processor_currencies', 'payment_processor_id', 'currency_id');
    }

    /**
     * @return HasOne
     */
    public function settings(): HasOne
    {
        return $this->hasOne(PaymentProcessorSetting::class);
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function appendScore(float $amount): self
    {
        $this->score = $this->computeScore($amount);
        return $this;
    }

    /**
     * @param float $amount
     * @return int
     */
    public function computeScore(float $amount): int
    {
        $settings = $this->settings;
        $score = 0;
        if (!$settings) {
            return $score;
        }

        $scoring_rules = config('blinqpay.routing_rules');
        $score += $this->calibrateTransactionCost($scoring_rules['transaction_cost'], $amount);
        $score += $this->calibrateReliability($scoring_rules['reliability'], $settings->reliability);
        return $score;
    }

    /**
     * @param array $rule
     * @param float $amount
     * @return int
     */
    protected function calibrateTransactionCost(array $rule, float $amount): int
    {
        $setting = $this->settings;
        $transaction_cost = $amount * ($this->fees_percentage / 100);
        $transaction_cost = ($setting->fees_cap && $transaction_cost > $setting->fees_cap) ? $setting->fees_cap : $transaction_cost;

        return match (true) {
            $transaction_cost <= $rule['min'] || $transaction_cost < $rule['medium'] => 3,
            $transaction_cost >= $rule['medium'] && $transaction_cost < $rule['high'] => 2,
            $transaction_cost >= $rule['high'] => 1,
            default => 0
        };
    }

    /**
     * @param array $rule
     * @param int $reliability
     * @return int
     */
    protected function calibrateReliability(array $rule, int $reliability): int
    {
        return match (true) {
            $reliability >= $rule['high'] => 3,
            $reliability >= $rule['medium'] && $reliability < $rule['high'] => 2,
            $reliability >= $rule['min'] || $reliability < $rule['medium'] => 1,
            default => 0
        };
    }
}
