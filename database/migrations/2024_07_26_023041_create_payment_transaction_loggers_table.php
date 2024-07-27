<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ajosav\Blinqpay\Models\BlinqpayCurrency;

class CreatePaymentTransactionLoggersTable extends Migration
{
    public function up()
    {
        Schema::create('payment_transaction_loggers', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('payment_processor_id')->constrained('payment_processors')->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->bigInteger('amount');
            $table->foreignIdFor(BlinqpayCurrency::class);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('payment_transaction_loggers');
    }
}