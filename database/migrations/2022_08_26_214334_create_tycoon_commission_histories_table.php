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
        Schema::create('tycoon_commission_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_tycoon_id')->nullable();
            $table->unsignedBigInteger('to_tycoon_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->string('bonus_type', 100);
            $table->float('amount', $precision = 19, $scale = 2);
            $table->tinyInteger('type')->default(2)->comment('1=admin, 2= tycoon');
            $table->foreign('from_tycoon_id')->references('id')->on('tycoons');
            $table->foreign('to_tycoon_id')->references('id')->on('tycoons');
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
        Schema::dropIfExists('tycoon_commission_histories');
    }
};
