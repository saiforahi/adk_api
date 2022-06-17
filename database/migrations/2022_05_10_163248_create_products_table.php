<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->unsignedBigInteger('sub_sub_category_id')->nullable();
            $table->string('name', 200);
            $table->mediumText('sort_desc')->nullable();
            $table->mediumText('property_options')->nullable();
            $table->string('unit')->nullable();
            $table->string('weight', 100)->nullable();
            $table->string('length', 10)->nullable();
            $table->string('width', 10)->nullable();
            $table->string('height', 10)->nullable();
            $table->string('product_type', 20)->nullable();
            $table->mediumText('colors')->nullable();
            $table->string('attributes', 1000)->nullable();
            $table->mediumText('attribute_options')->nullable();
            $table->boolean('order_quantity_limit')->nullable();
            $table->string('order_quantity_max')->nullable();
            $table->string('order_quantity_min')->nullable();
            $table->boolean('price_type')->nullable()->default(0);
            $table->double('unit_price', 8, 2)->nullable();
            $table->integer('currency_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->longText('description')->nullable();
            $table->integer('num_of_sale')->nullable();
            $table->mediumText('slug')->nullable();
            $table->boolean('digital')->nullable();
            $table->nullableMorphs('added_by');
            $table->softDeletes();
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
        Schema::dropIfExists('products');
    }
};
