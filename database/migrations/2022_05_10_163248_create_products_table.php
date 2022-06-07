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
            $table->string('name');
            $table->boolean('category_label')->nullable();
            $table->mediumText('property_options')->nullable();
            $table->enum('unit_type', ['size', 'weight', 'quantity']);
            $table->string('unit_value');
            $table->integer('unit')->nullable();
            $table->string('weight', 100)->nullable();
            $table->string('length', 10)->nullable();
            $table->string('width', 10)->nullable();
            $table->string('height', 10)->nullable();
            $table->mediumText('tags')->nullable();
            $table->string('product_type', 20)->nullable();
            $table->string('photos', 2000)->nullable();
            $table->string('thumbnail_img', 100)->nullable();
            $table->string('featured_img', 100)->nullable();
            $table->string('flash_deal_img', 100)->nullable();
            $table->string('video_link', 100)->nullable();
            $table->mediumText('colors')->nullable();
            $table->mediumText('color_image')->nullable();
            $table->boolean('color_type')->default(0);
            $table->string('attributes', 1000)->nullable();
            $table->mediumText('attribute_options')->nullable();
            $table->double('tax')->nullable();
            $table->string('tax_type', 10)->nullable();
            $table->double('discount')->nullable();
            $table->string('discount_type', 10)->nullable();
            $table->boolean('discount_variation')->default(0);
            $table->boolean('order_quantity_limit')->default(0);
            $table->string('order_quantity_max')->nullable();
            $table->string('order_quantity_min')->nullable();
            $table->boolean('price_type')->default(0);
            $table->boolean('stock_management')->default(1);
            $table->double('unit_price')->nullable();
            $table->integer('currency_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('sku')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('shipping_type')->default(0);
            $table->double('shipping_cost')->default(0.00)->nullable();
            $table->integer('num_of_sale')->default(0);
            $table->mediumText('meta_title')->nullable();
            $table->longText('meta_description')->nullable();
            $table->string('meta_img')->nullable();
            $table->mediumText('slug');
            $table->double('rating')->default(0.00);
            $table->boolean('digital')->default(0);
            $table->string('added_by', 50);
            $table->integer('user_id')->nullable();
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
