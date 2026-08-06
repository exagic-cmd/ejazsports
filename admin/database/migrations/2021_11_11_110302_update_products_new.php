<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductsNew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('title');
            $table->string('code');
            $table->string('slug')->nullable();
            $table->string('menu_text')->nullable();
            $table->string('product_heading')->nullable();
            $table->text('description')->nullable();
            $table->text('keywords')->nullable();
            $table->text('short_description')->nullable();
            $table->text('long_description')->nullable();
            $table->text('ingredients')->nullable();
            $table->text('how_to_use')->nullable();
            $table->text('about_brand')->nullable();

            $table->unsignedBigInteger('brand_id');
            $table->foreign('brand_id')
                ->references('id')
                ->on('brands')
                ->onDelete('cascade');

            $table->float('price')->default(0);
            $table->float('discount_amount')->default(0);
            $table->boolean('is_product_discount')->default(0);
            $table->boolean('is_brand_discount')->default(0);
            $table->boolean('is_category_discount')->default(0);
            $table->boolean('discount_status')->default(0);
            $table->integer('implement_discount_id')->default(0);

            $table->boolean('is_new')->default(0);
            $table->boolean('is_featured')->default(0);
            $table->boolean('is_premium')->default(0);
            $table->boolean('is_last_pick')->default(0);
            $table->boolean('have_variants')->default(0);

            $table->integer('available_stock')->default(0);
            $table->boolean('is_in_stock')->default(0);
            $table->boolean('status')->default(1);

            $table->integer('serial_no')->default(0);
            $table->integer('re_order_level')->default(0);
            $table->float('weight')->default(0);

            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
