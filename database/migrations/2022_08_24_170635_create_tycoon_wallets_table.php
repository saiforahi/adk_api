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
        Schema::create('tycoon_wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tycoon_id')->unique();
            $table->float('main_balance', $precision = 19, $scale = 2)->default(0);
            $table->float('product_balance', $precision = 19, $scale = 2)->default(0);
            $table->float('marketing_balance', $precision = 19, $scale = 2)->default(0);
            $table->float('sales_commission', $precision = 19, $scale = 2)->default(0);
            $table->float('dealer_ref_comission', $precision = 19, $scale = 2)->default(0);
            $table->float('group_commission', $precision = 19, $scale = 2)->default(0);
            $table->float('group_star_balance', $precision = 19, $scale = 2)->default(0);
            $table->float('monthly_salary', $precision = 19, $scale = 2)->default(0);
            $table->float('incentive', $precision = 19, $scale = 2)->default(0);
            $table->float('provident_fund', $precision = 19, $scale = 2)->default(0);
            $table->float('withdraw', $precision = 19, $scale = 2)->default(0);
            $table->foreign('tycoon_id')->references('id')->on('tycoons');
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
        Schema::dropIfExists('tycoon_wallets');
    }
};
