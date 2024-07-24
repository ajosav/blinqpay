<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentProcessorSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('payment_processor_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('payment_processor_id')->constrained('payment_processors')->cascadeOnDelete();
            $table->double('fees_percentage')->comment('Percentage cost of a transaction');
            $table->double('fees_cap')->nullable()->comment('Maximum that can be charged on a transaction');
            $table->integer('reliability')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('payment_processor_settings');
    }
}