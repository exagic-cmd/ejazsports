<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_out_id')->nullable();
            $table->foreign('store_out_id')
                ->references('id')
                ->on('stores')
                ->onDelete('SET NULL');
            $table->unsignedBigInteger('store_in_id')->nullable();
            $table->foreign('store_in_id')
                ->references('id')
                ->on('stores')
                ->onDelete('SET NULL');
            $table->date('send_date');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('SET NULL');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
                ->onDelete('SET NULL');
            $table->unsignedBigInteger('received_by')->nullable();
            $table->foreign('received_by')
                ->references('id')
                ->on('users')
                ->onDelete('SET NULL');
            $table->date('received_date')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('type')->default(1);
            $table->integer('total_products')->default(0);
            $table->integer('total_product_qty')->default(0);
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
        Schema::dropIfExists('supplies');
    }
}
