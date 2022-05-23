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
        Schema::create('dealers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dealer_type_id')->nullable();
            $table->integer('capital')->nullable();
            $table->integer('product_price')->nullable();
            $table->float('commission')->nullable();
            $table->timestamps();
            $table->foreign('dealer_type_id')->references('id')->on('dealer_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dealers');
    }
};
