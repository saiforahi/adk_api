<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dealer_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_dealer_id')->nullable();
            $table->unsignedBigInteger('to_dealer_id')->nullable();
            $table->unsignedBigInteger('tycoon_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->float('amount', $precision = 19, $scale = 2);
            $table->foreign('from_dealer_id')->references('id')->on('dealers');
            $table->foreign('to_dealer_id')->references('id')->on('dealers');
            $table->foreign('tycoon_id')->references('id')->on('tycoons');
            $table->foreign('product_id')->references('id')->on('products');
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
        Schema::dropIfExists('dealer_commissions');
    }
};
