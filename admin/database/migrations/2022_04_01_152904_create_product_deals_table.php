<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_deals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code')->nullable();
            $table->string('slug')->nullable();
            $table->string('menu_text')->nullable();
            $table->string('deal_heading')->nullable();
            $table->text('description')->nullable();
            $table->text('keywords')->nullable();
            $table->text('short_description')->nullable();
            $table->text('long_description')->nullable();
            $table->text('ingredients')->nullable();
            $table->text('how_to_use')->nullable();
            $table->text('about_brand')->nullable();

            $table->float('price')->default(0);
            $table->float('discount_amount')->default(0);

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
        Schema::dropIfExists('product_deals');
    }
}
