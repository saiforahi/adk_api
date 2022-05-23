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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->unsignedBigInteger('dealer_id')->comment('Product stocked by dealer id');
            $table->foreign('dealer_id')->references('id')->on('dealers');
            $table->enum('unit_type',['size','weight','quantity']);
            $table->string('unit_value');
            $table->string('stock_place');
            $table->dateTime('stock_in');
            $table->dateTime('stock_out');
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
        Schema::dropIfExists('stocks');
    }
};
