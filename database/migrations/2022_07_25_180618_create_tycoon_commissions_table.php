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
        Schema::create('tycoon_commissions', function (Blueprint $table) {
            $table->id();
            $table->double('instant_sale_bonus');
            $table->double('group_bonus');
            $table->double('commission')->nullable();
            $table->double('adk_provident_fund')->nullable();
            $table->double('tycoon_provident_fund')->nullable();
            $table->double('dealer_ref_comm')->nullable();
            $table->double('monthly_star_bonus')->nullable();
            $table->double('monthly_sallary')->nullable();
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
        Schema::dropIfExists('tycoon_commissions');
    }
};
