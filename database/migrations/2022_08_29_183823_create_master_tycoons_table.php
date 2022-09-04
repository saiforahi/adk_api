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
        Schema::create('master_tycoons', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->unique();
            $table->string('username')->unique();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('phone')->unique()->nullable();
            $table->string('post_code')->nullable();
            $table->string('nominee_name')->nullable();
            $table->string('nid')->nullable();
            $table->float('opening_balance')->default(0.0);
            $table->string('nominee_phone')->nullable();
            $table->string('nominee_nid')->nullable();
            $table->mediumText('address')->nullable();
            $table->mediumText('bank_details')->nullable();
            $table->mediumText('nominee_address')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('division_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('thana_id')->nullable();
            $table->boolean('is_active')->default(true);
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
            $table->rememberToken();
            $table->timestamps();
            $table->foreign('division_id')->references('id')->on('divisions');
            $table->foreign('district_id')->references('id')->on('districts');
            $table->foreign('thana_id')->references('id')->on('thanas');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_tycoons');
    }
};
