<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->double('delivery_charges')->default(0);
            $table->double('discount_amount')->default(0);
            $table->double('total_amount')->default(0);
            $table->tinyInteger('payment_method');
            $table->double('paid_amount')->default(0);
            $table->text('fbr_invoice_id')->nullable();
            $table->text('fbr_return_invoice_id')->nullable();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreign('store_id')
                ->references('id')
                ->on('stores')
                ->onDelete('SET NULL');
            $table->integer('total_products')->default(0);
            $table->integer('total_quantity')->default(0);
            $table->timestamp('return_date')->nullable();
            $table->double('return_amount')->nullable();
            $table->text('additional_notes')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
