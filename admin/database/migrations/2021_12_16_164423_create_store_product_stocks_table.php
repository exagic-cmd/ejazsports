<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreProductStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_product_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreign('store_id')
                ->references('id')
                ->on('stores')
                ->onDelete('SET NULL');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('SET NULL');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->foreign('variant_id')
                ->references('id')
                ->on('product_variants')
                ->onDelete('SET NULL');
            $table->unsignedBigInteger('receiving_id')->nullable();
//            $table->foreign('receiving_id')
//                ->references('id')
//                ->on('receivings')
//                ->onDelete('SET NULL');
            $table->unsignedBigInteger('supply_id')->nullable();
            $table->foreign('supply_id')
                ->references('id')
                ->on('supplies')
                ->onDelete('SET NULL');
            $table->date('expiry_date')->nullable();
            $table->integer('purchase_qty')->default(0);
            $table->integer('sold_qty')->default(0);
            $table->integer('cost')->default(0);
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
        Schema::dropIfExists('store_product_stocks');
    }
}
