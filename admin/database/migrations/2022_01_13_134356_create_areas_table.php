<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('delivery_charges');
            $table->integer('min_order_amount')->default(0);
            $table->integer('delivery_charges_above')->default(0);
            $table->boolean('status')->default(1);
            $table->float('min_weight_allow')->default(1500);
            $table->float('extra_charges_per_g_ml')->default(0);
            $table->integer('serial_no')->nullable();
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
        Schema::dropIfExists('areas');
    }
}
