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
        Schema::create('dealer_bonus_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dealer_type_id')->nullable();
            $table->double('capital');
            $table->double('product');
            $table->double('commission')->nullable();
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
        Schema::dropIfExists('dealer_bonus_configs');
    }
};
