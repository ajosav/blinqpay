<?php

use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentProcessorSettingFactory extends Factory
{
    public function definition()
    {
        return [
            'fees_percentage' => $this->faker->randomFloat(2, 0.1, 6),
            'fees_cap' => rand(1000, 5000),
            'reliability' => rand(1, 5)
        ];
    }
}