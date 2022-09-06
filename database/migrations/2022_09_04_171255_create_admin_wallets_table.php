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
        Schema::create('admin_wallets', function (Blueprint $table) {
            $table->id();
            $table->float('product_balance', $precision = 19, $scale = 2)->default(0);
            $table->float('marketing_balance', $precision = 19, $scale = 2)->default(0);
            $table->float('stock_balance', $precision = 19, $scale = 2)->default(0);
            $table->float('total_sale', $precision = 19, $scale = 2)->default(0);
            $table->float('sales_commission', $precision = 19, $scale = 2)->default(0);
            $table->float('dealer_marketing_commission', $precision = 19, $scale = 2)->default(0);
            $table->float('tycoon_group_commission', $precision = 19, $scale = 2)->default(0);
            $table->float('tycoon_group_commission_gap', $precision = 19, $scale = 2)->default(0);
            $table->float('tycoon_star_balance_gap', $precision = 19, $scale = 2)->default(0);
            $table->float('provident_fund_gap', $precision = 19, $scale = 2)->default(0);
            $table->float('withdraw', $precision = 19, $scale = 2)->default(0);
            $table->float('incentive', $precision = 19, $scale = 2)->default(0);
            $table->float('tycoon_star_balance', $precision = 19, $scale = 2)->default(0);
            $table->float('monthly_sales', $precision = 19, $scale = 2)->default(0);
            $table->float('expense', $precision = 19, $scale = 2)->default(0);
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
        Schema::dropIfExists('admin_wallets');
    }
};
