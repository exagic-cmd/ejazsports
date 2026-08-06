<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('meta_description')->nullable();
            $table->text('keywords')->nullable();
            $table->string('menu_text');
            $table->string('category_heading');
            $table->integer('serial_no')->default(0);
            $table->integer('serial_no_brand_listing')->default(0);
            $table->text('image');
            $table->text('banner_image')->nullable();
            $table->text('mobile_banner_image')->nullable();
            $table->text('brand_listing_image')->nullable();
            $table->integer('discount_id')->nullable();
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
        Schema::dropIfExists('categories');
    }
}
