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
        Schema::create('pre_dealers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pre_dealer_type_id')->nullable();
            $table->timestamps();
            $table->foreign('pre_dealer_type_id')->references('id')->on('pre_dealer_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pre_dealers');
    }
};
