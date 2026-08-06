<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourierHandoverOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courier_handover_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('courier_id')->nullable();
            $table->foreign('courier_id')
                ->references('id')
                ->on('couriers')
                ->onDelete('SET NULL');
            $table->integer('total_orders');
            $table->double('total_amount');
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
        Schema::dropIfExists('courier_handover_orders');
    }
}
