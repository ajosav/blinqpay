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
            $table->double('fees_percentage')->comment('Percentage cost of each transactions');
            $table->double('fees_cap')->comment('Maximum that can be charged on a transaction');
            $table->integer('stability_rating')->default(1)->comment('Rating scale of 1 - 5. 1 representing very stable, and 5 representing less stable.');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('payment_processor_settings');
    }
}