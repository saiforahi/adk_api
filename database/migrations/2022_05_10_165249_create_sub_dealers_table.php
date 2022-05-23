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
        Schema::create('sub_dealers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_dealer_type_id')->nullable();
            $table->timestamps();
            $table->foreign('sub_dealer_type_id')->references('id')->on('sub_dealer_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_dealers');
    }
};
