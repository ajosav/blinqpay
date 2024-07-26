<?php

use Database\Seeders\CurrencySeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class CreateBlinqpayCurrenciesTable extends Migration
{
    public function up()
    {
        Schema::create('blinqpay_currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('code');
            $table->timestamps();
        });

        Artisan::call('db:seed', [
            '--class' => CurrencySeeder::class
        ]);
    }

    public function down()
    {
        Schema::drop('blinqpay_currencies');
    }
}