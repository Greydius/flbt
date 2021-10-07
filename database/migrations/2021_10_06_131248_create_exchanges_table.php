<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExchangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('clients');
            $table->foreignId('buyer_id')->constrained('clients');

            $table->foreignId('coin_id')->constrained();
            $table->foreignId('fiat_id')->constrained();

            $table->double('coin_value', 30, 20);
            $table->double('fiat_value', 30, 2);
            $table->enum('type', ['sell', 'buy']);
            $table->enum('status', ['open', 'proccess', 'done', 'error']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exchanges');
    }
}
