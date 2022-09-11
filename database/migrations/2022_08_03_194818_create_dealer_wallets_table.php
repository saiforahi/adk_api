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
        Schema::create('dealer_wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dealer_id')->unique();
            $table->foreign('dealer_id')->references('id')->on('dealers');
            $table->float('total_income', $precision = 19, $scale = 2)->default(0);
            $table->float('product_balance', $precision = 19, $scale = 2)->default(0);
            $table->float('stock_balance', $precision = 19, $scale = 2)->default(0);
            $table->float('sales_balance', $precision = 19, $scale = 2)->default(0);
            $table->float('main_balance', $precision = 19, $scale = 2)->default(0);
            $table->float('profit', $precision = 19, $scale = 2)->default(0);
            $table->float('withwraw', $precision = 19, $scale = 2)->default(0);
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
        Schema::dropIfExists('dealer_wallets');
    }
};
