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
        Schema::create('dealer_order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dealer_id');
            $table->float('total_amount');
            $table->string('cus_first_name')->comment('customer first name');
            $table->string('cus_last_name')->nullable()->comment('customer last name');
            $table->string('cus_email')->nullable()->comment('customer email');
            $table->string('cus_phone')->nullable()->comment('customer phone');
            $table->string('cus_address')->nullable()->comment('customer address');
            $table->string('promo_code')->nullable()->comment('applied promo code');
            $table->mediumText('order_notes')->nullable();
            $table->timestamps();

            $table->foreign('dealer_id')->references('id')->on('dealers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dealer_order_details');
    }
};
