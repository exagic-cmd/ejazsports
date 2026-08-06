<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplyProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supply_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supply_id')->nullable();
            $table->foreign('supply_id')
                ->references('id')
                ->on('supplies')
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
            $table->integer('qty');
            $table->integer('received_qty')->nullable();
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
        Schema::dropIfExists('supply_products');
    }
}
