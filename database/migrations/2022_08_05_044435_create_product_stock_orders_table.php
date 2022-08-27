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
        Schema::create('product_stock_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->nullableMorphs('order_from');
            $table->nullableMorphs('order_to');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('qty')->comment('product quantity');
            $table->float('price', $precision = 19, $scale = 2);
            $table->mediumText('order_notes')->nullable();
            $table->enum('status',['APPROVED','PENDING','PROCESSED'])->default('PENDING');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
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
        Schema::dropIfExists('product_stock_orders');
    }
};
