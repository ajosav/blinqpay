<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentProcessorCurrenciesTable extends Migration
{
    public function up()
    {
        Schema::create('payment_processor_currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('payment_processor_id')->constrained('payment_processors')->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained('blinqpay_currencies')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::drop('payment_processor_currencies');
    }
}