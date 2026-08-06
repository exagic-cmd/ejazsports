<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receivings', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no');
            $table->date('date');
            $table->unsignedBigInteger('po_id')->nullable();
            $table->foreign('po_id')
                ->references('id')
                ->on('purchase_orders')
                ->onDelete('SET NULL');
            $table->boolean('payment_method')->nullable();
            $table->double('gross_amount')->default(0);
            $table->double('tax')->default(0);
            $table->double('discount')->default(0);
            $table->double('net_amount')->default(0);
            $table->double('total_products')->default(0);
            $table->double('total_qty')->default(0);
            $table->text('file')->nullable();
            $table->boolean('status');
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
            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreign('store_id')
                ->references('id')
                ->on('stores')
                ->onDelete('SET NULL');
            $table->text('comment')->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('receivings');
    }
}
