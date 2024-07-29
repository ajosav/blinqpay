<?php

namespace Database\Seeders;

use Ajosav\Blinqpay\Models\BlinqpayCurrency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CurrencySeeder extends Seeder
{
    public const SEED_CHUNK_SIZE = 500;

    /**
     * Run the database seeds
     */
    public function run()
    {
        $file_exists = File::exists(__DIR__ . '/../json_data/currencies.json');
        if ($file_exists) {
            $currencies = json_decode(File::get(__DIR__ . '/../json_data/currencies.json'), true);
            $currencies_chunk = array_chunk(
                array_map(fn($currency) => ['name' => $currency['name'], 'code' => $currency['code']], $currencies),
                self::SEED_CHUNK_SIZE
            );

            foreach ($currencies_chunk as $currency_data) {
                BlinqpayCurrency::upsert($currency_data, ['name'], ['name', 'code']);
            }
        }
    }
}
