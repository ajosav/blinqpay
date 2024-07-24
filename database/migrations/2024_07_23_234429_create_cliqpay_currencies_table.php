<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCliqpayCurrenciesTable extends Migration
{
    public function up()
    {
        Schema::create('cliqpay_currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('code');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('cliqpay_currencies');
    }
}