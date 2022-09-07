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
        Schema::create('balance_transfers', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('transfer_from_type');
            $table->nullableMorphs('transfer_to_type');
            $table->float('amount', $precision = 19, $scale = 2);
            $table->tinyInteger('payment_type');
            $table->enum('status',['APPROVED','PENDING','PROCESSED'])->default('PENDING');
            $table->timestamps();
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
        Schema::dropIfExists('balance_transfers');
    }
};
