<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('meta_description')->nullable();
            $table->text('keywords')->nullable();
            $table->string('menu_text');
            $table->string('brand_heading');
            $table->integer('serial_no')->default(0);
            $table->text('image');
            $table->text('banner_image')->nullable();
            $table->text('mobile_banner_image')->nullable();
            $table->boolean('status')->default(0);
            $table->string('slug');
            $table->boolean('is_premium')->default(0);
            $table->text('premium_image')->nullable();
            $table->boolean('is_featured')->default(0);
            $table->text('featured_image')->nullable();
            $table->integer('discount_id')->nullable();
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
        Schema::dropIfExists('brands');
    }
}
