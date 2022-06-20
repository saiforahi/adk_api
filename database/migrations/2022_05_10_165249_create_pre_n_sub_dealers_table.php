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
        Schema::create('pre_n_sub_dealers', function (Blueprint $table) {
            $table->id();
            $table->enum('type',['sub','pre']);
            $table->unsignedBigInteger('sub_dealer_type_id')->nullable();
            $table->unsignedBigInteger('pre_dealer_type_id')->nullable();
            $table->string('user_id')->unique();
            $table->string('username')->unique();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->mediumText('address')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('placement_id');
            $table->float('opening_balance');
            $table->string('password');
            $table->rememberToken();
            $table->dateTime('type_updated_at')->nullable();
            $table->timestamps();
            $table->foreign('sub_dealer_type_id')->references('id')->on('sub_dealer_types');
            $table->foreign('pre_dealer_type_id')->references('id')->on('pre_dealer_types');
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
        Schema::dropIfExists('pre_n_sub_dealers');
    }
};
