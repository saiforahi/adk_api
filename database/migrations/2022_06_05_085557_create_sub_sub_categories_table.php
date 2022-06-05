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
        Schema::create('sub_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_category_id');
            $table->string('name', 50);
            $table->string('slug');
            $table->double('commission_rate', 8, 2)->default(0.00);
            $table->string('banner', 100)->nullable();
            $table->string('icon', 100)->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('digital')->default(false);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('sub_category_id')->references('id')->on('sub_categories')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_sub_categories');
    }
};
