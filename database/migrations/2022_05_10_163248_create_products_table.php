<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->mediumText('sort_desc')->nullable();
            $table->nullableMorphs('added_by');
            $table->integer('category_id')->nullable();
            $table->integer('subcategory_id')->nullable();
            $table->integer('subsubcategory_id')->nullable();
            $table->mediumText('property_options')->nullable();
            $table->integer('brand_id')->nullable();
            $table->integer('unit')->nullable();
            $table->string('weight', 100)->nullable();
            $table->string('length', 10)->nullable();
            $table->string('width', 10)->nullable();
            $table->string('height', 10)->nullable();
            $table->string('product_type', 20)->nullable();
            $table->mediumText('colors')->nullable();
            $table->string('attributes', 1000)->nullable();
            $table->mediumText('attribute_options')->nullable();
            $table->boolean('orderQtyLimit')->default(0);
            $table->string('orderQtyLimitMax', 10)->nullable();
            $table->string('orderQtyLimitMin', 10)->nullable();
            $table->double('unit_price', 8, 2)->nullable();
            $table->integer('currency_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->longText('description')->nullable();
            $table->integer('num_of_sale')->default(0);
            $table->mediumText('slug');
            $table->boolean('digital')->default(0);
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
}
