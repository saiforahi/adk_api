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
        Schema::create('balance_processes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tycoon_id');
            $table->string('balance_type');
            $table->float('amount', $precision = 19, $scale = 2);
            $table->timestamps();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->foreign('tycoon_id')->references('id')->on('tycoons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('balance_processes');
    }
};
