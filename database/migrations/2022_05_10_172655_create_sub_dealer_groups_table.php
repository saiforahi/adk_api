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
        Schema::create('sub_dealer_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_dealer_id');
            $table->foreign('sub_dealer_id')->references('id')->on('pre_n_sub_dealers');
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
        Schema::dropIfExists('sub_dealer_groups');
    }
};
