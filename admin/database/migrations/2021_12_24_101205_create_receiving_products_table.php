<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivingProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receiving_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receiving_id')->nullable();
            $table->foreign('receiving_id')
                ->references('id')
                ->on('receivings')
                ->onDelete('SET NULL');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('SET NULL');
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->foreign('product_variant_id')
                ->references('id')
                ->on('product_variants')
                ->onDelete('SET NULL');
            $table->integer('qty');
            $table->double('trade_price');
            $table->double('gst')->default(0);
            $table->double('discount')->default(0);
            $table->double('cost_price')->default(0);
            $table->double('sale_price')->default(0);
            $table->boolean('foc_product')->default(false);
            $table->boolean('tester_product')->default(false);
            $table->boolean('po_product')->default(true);
            $table->date('expiry_date')->nullable();

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
        Schema::dropIfExists('receiving_products');
    }
}
